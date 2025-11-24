import os
import joblib
import numpy as np
import pandas as pd
from sqlalchemy import create_engine

engine = create_engine("mysql+pymysql://root:@localhost/vendas")

# vendas com produtos
query_vendas = """
    SELECT 
        v.id AS venda_id,
        v.cliente_id,
        vi.produto_id
    FROM venda_item vi
    JOIN venda v ON v.id = vi.venda_id
"""
df = pd.read_sql(query_vendas, engine)

if df.empty:
    raise Exception("Nenhuma venda encontrada no banco de dados.")

# todos os produtos com info completa
query_produtos = """
    SELECT 
        id AS produto_id,
        nome AS produto_nome,
        categoria,
        preco
    FROM produto
"""
produtos = pd.read_sql(query_produtos, engine)

if produtos.empty:
    raise Exception("Nenhum produto encontrado no banco de dados.")

produtos["preco"] = pd.to_numeric(produtos["preco"], errors="coerce").fillna(0.0)

produtos = produtos.drop_duplicates(subset=["produto_id"]).sort_values("produto_id").reset_index(drop=True)

id_to_idx = {pid: idx for idx, pid in enumerate(produtos["produto_id"].tolist())}

vendas_group = df.groupby("venda_id")["produto_id"].apply(lambda x: list(set(x))).reset_index()

num_vendas = len(vendas_group)
num_produtos = len(produtos)

if num_vendas == 0 or num_produtos == 0:
    raise Exception("Dados insuficientes para treinar o modelo.")

support = np.zeros(num_produtos, dtype=np.float64)

# matriz de coocorrência conta quantas vezes produtos aparecem juntos
cooc = np.zeros((num_produtos, num_produtos), dtype=np.float64)

for _, row in vendas_group.iterrows():
    itens_venda = [id_to_idx[pid] for pid in row["produto_id"] if pid in id_to_idx]

    if not itens_venda:
        continue

    for i in itens_venda:
        support[i] += 1

    # marca pares de produtos que foram comprados juntos
    if len(itens_venda) > 1:
        for i in range(len(itens_venda)):
            for j in range(i + 1, len(itens_venda)):
                a = itens_venda[i]
                b = itens_venda[j]
                cooc[a, b] += 1
                cooc[b, a] += 1

support[support == 0] = 1e-9

# matrizes de similaridade
jaccard = np.zeros_like(cooc)
lift = np.zeros_like(cooc)

for i in range(num_produtos):
    for j in range(num_produtos):
        if i == j:
            continue
        inter = cooc[i, j]
        if inter <= 0:
            continue

        # jaccard mede similaridade entre conjuntos
        union = support[i] + support[j] - inter
        if union > 0:
            jaccard[i, j] = inter / union

        # lift aumenta peso de combinações que acontecem acima do esperado
        lift[i, j] = (inter * num_vendas) / (support[i] * support[j])

def normalize_matrix(mat):
    m = mat.copy()
    mask = m > 0
    if not np.any(mask):
        return m
    min_val = m[mask].min()
    max_val = m[mask].max()
    if max_val - min_val < 1e-9:
        m[mask] = 1.0
        return m
    m[mask] = (m[mask] - min_val) / (max_val - min_val)
    return m

# normalização das métricas
jaccard_norm = normalize_matrix(jaccard)
lift_norm = normalize_matrix(lift)

# combinação Jaccard + Lift (modelo híbrido)
sim_matrix = 0.5 * jaccard_norm + 0.5 * lift_norm

np.fill_diagonal(sim_matrix, 0.0)

base_dir = os.path.dirname(__file__)
joblib.dump(produtos, os.path.join(base_dir, "produtos.pkl"))  # tabela organizada com todos os produtos
joblib.dump(sim_matrix, os.path.join(base_dir, "sim_cooc.pkl"))  # matriz de similaridade q mostra quais produtos costumam aparecer juntos nas compras

support_series = pd.Series(
    data=support,
    index=produtos["produto_id"]
)
joblib.dump(support_series, os.path.join(base_dir, "support.pkl")) # popularidade de cada produto no banco (quantas vezes aparece nas vendas)

print("✅ Modelo de co-ocorrência (Jaccard + Lift) treinado com sucesso!")
print(f"Total de produtos: {num_produtos}")
print(f"Total de vendas processadas: {num_vendas}")
