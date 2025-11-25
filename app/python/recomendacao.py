import os
import sys
import io
import json
import joblib
import numpy as np
import pandas as pd
from sqlalchemy import create_engine

base_dir = os.path.dirname(__file__)
sim_path = os.path.join(base_dir, "sim_cooc.pkl")
prod_path = os.path.join(base_dir, "produtos.pkl")
support_path = os.path.join(base_dir, "support.pkl")

try:
    sim_matrix = joblib.load(sim_path)
    produtos = joblib.load(prod_path) 
    support = joblib.load(support_path)
except Exception as e:
    print(json.dumps({"erro": f"Erro ao carregar modelos: {str(e)}"}, ensure_ascii=False))
    sys.exit(1)

produtos = produtos.reset_index(drop=True)

# cria um map produto_id índice da matriz de similaridade
id_to_idx = {pid: idx for idx, pid in enumerate(produtos["produto_id"].tolist())}

engine = create_engine("mysql+pymysql://root:@localhost/vendas")


def recomendar_produtos(cliente_id, top_n=3):
    try:
        query = f"""
            SELECT 
                vi.produto_id,
                SUM(CAST(vi.quantidade AS DECIMAL(10,2))) AS qtd
            FROM venda_item vi
            JOIN venda v ON v.id = vi.venda_id
            WHERE v.cliente_id = {cliente_id}
            GROUP BY vi.produto_id
        """
        compras = pd.read_sql(query, engine)

        # caso o cliente nunca comprou nada, usa popularidade
        if compras.empty:
            return recomendar_por_popularidade(top_n)

        compras = compras[compras["produto_id"].isin(id_to_idx.keys())]
        if compras.empty:
            return recomendar_por_popularidade(top_n)

        comprados_ids = compras["produto_id"].tolist()
        comprados_qtd = compras["qtd"].astype(float).tolist()

        comprados_idx = [id_to_idx[pid] for pid in comprados_ids]

        sim_sub = sim_matrix[comprados_idx, :]

        pesos = np.array(comprados_qtd, dtype=np.float64).reshape(-1, 1)

        # soma ponderada das similaridades
        soma_ponderada = (sim_sub * pesos).sum(axis=0)
        normalizador = pesos.sum() + 1e-9
        scores = soma_ponderada / normalizador

        for idx in comprados_idx:
            scores[idx] = -1.0

        # se tudo deu negativo usa popularidade como fallback
        if np.all(scores <= 0):
            return recomendar_por_popularidade(top_n, excluir_ids=comprados_ids)

        # normalização dos scores
        scores_pos = scores[scores > 0]
        if scores_pos.size == 0:
            return recomendar_por_popularidade(top_n, excluir_ids=comprados_ids)

        min_s = scores_pos.min()
        max_s = scores_pos.max()

        if max_s - min_s < 1e-9:
            scores_norm = np.where(scores > 0, 1.0, 0.0)
        else:
            scores_norm = np.zeros_like(scores)
            mask = scores > 0
            scores_norm[mask] = (scores[mask] - min_s) / (max_s - min_s)

        idx_ordenados = np.argsort(scores_norm)[::-1]

        recomendados = []
        for idx in idx_ordenados:
            if scores_norm[idx] <= 0:
                continue

            pid = int(produtos.loc[idx, "produto_id"])
            if pid in comprados_ids:
                continue

            recomendados.append((pid, float(scores_norm[idx])))
            if len(recomendados) >= top_n:
                break

        if len(recomendados) < top_n:
            faltam = top_n - len(recomendados)
            extra = recomendar_por_popularidade(
                faltam,
                excluir_ids=comprados_ids + [pid for pid, _ in recomendados],
                apenas_ids=True
            )
            recomendados.extend(extra)

        # monta o dicionário final das recomendações
        resultado = []
        for pid, score in recomendados:
            linha = produtos.loc[produtos["produto_id"] == pid].iloc[0]
            resultado.append({
                "produto_id": int(pid),
                "produto_nome": str(linha["produto_nome"]),
                "categoria": str(linha["categoria"]),
                "preco": float(linha["preco"]),
                "probabilidade_compra": round(score, 4)
            })

        return resultado

    except Exception as e:
        return {"erro": f"Erro ao gerar recomendações: {str(e)}"}


def recomendar_por_popularidade(top_n=3, excluir_ids=None, apenas_ids=False):
    """
    fallback caso o modelo não consiga recomendar baseado no histórico
    """
    if excluir_ids is None:
        excluir_ids = []

    sup = support.copy()

    for pid in excluir_ids:
        if pid in sup.index:
            sup.loc[pid] = -1

    sup = sup.sort_values(ascending=False)
    sup = sup[sup > 0]

    recomendados = []
    for pid, val in sup.head(top_n).items():
        score_fake = 0.5
        recomendados.append((int(pid), float(score_fake)))

    if apenas_ids:
        return recomendados

    # transforma em dicionário igual ao das recomendações normais
    resultado = []
    for pid, score in recomendados:
        if pid not in produtos["produto_id"].values:
            continue
        linha = produtos.loc[produtos["produto_id"] == pid].iloc[0]
        resultado.append({
            "produto_id": int(pid),
            "produto_nome": str(linha["produto_nome"]),
            "categoria": str(linha["categoria"]),
            "preco": float(linha["preco"]),
            "probabilidade_compra": round(score, 4)
        })

    return resultado

if __name__ == "__main__":
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

    # testar no terminal
    if len(sys.argv) > 1:
        try:
            cliente_id = int(sys.argv[1])
            recomendacoes = recomendar_produtos(cliente_id)
            print(json.dumps(recomendacoes, ensure_ascii=False, indent=2))
        except Exception as e:
            print(json.dumps({"erro": str(e)}, ensure_ascii=False))
    else:
        print(json.dumps([], ensure_ascii=False))
