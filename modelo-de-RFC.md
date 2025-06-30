# Capa
RFC - GESTÃO DE VENDAS  
Título do Projeto: Sistema Inteligente de Gestão de Vendas  
Nome do Estudante: Lucas Bortoloti  
Curso: Engenharia de Software  
Data de Entrega: 01/04/2025  

# Resumo
Este documento apresenta os requisitos funcionais e não funcionais do Sistema Inteligente 
de Gestão de Vendas. O projeto tem como diferencial um módulo de recomendação voltado 
aos vendedores, sugerindo produtos a oferecer com base no histórico de compras dos 
clientes. O documento abrange desde o contexto e justificativa até a arquitetura e 
tecnologias utilizadas.

# 1. Introdução

## 1.1 Contexto
A gestão de vendas é essencial para o sucesso de qualquer empresa. No entanto, 
vendedores frequentemente dependem de experiência prévia ou intuição para recomendar 
produtos aos clientes. Este projeto visa introduzir inteligência artificial para automatizar e 
otimizar esse processo.

## 1.2 Justificativa
A maioria dos sistemas de gestão de vendas foca em controle administrativo e relatórios, 
mas poucos oferecem suporte estratégico aos vendedores. A recomendação de produtos 
baseada em dados pode aumentar a taxa de conversão e a satisfação do cliente.  

## 1.3 Objetivos
Objetivo principal: Desenvolver um sistema de gestão de vendas com um mecanismo de 
recomendação de produtos para vendedores.  
Objetivos secundários:  
● Implementar relatórios e gráficos.  
● Criar um sistema escalável e seguro.  
● Utilizar IA para melhorar estratégias de venda.

# 2. Descrição do Projeto

## 2.1 Tema do Projeto
Sistema de gestão de vendas com recomendação inteligente para vendedores. 

## 2.2 Problemas a Resolver  
● Dificuldade dos vendedores em identificar oportunidades.  
● Baixa personalização na oferta de produtos.  

## 2.3 Limitações 
● O sistema não realizará automação de pedidos.  
● A recomendação se baseará apenas no histórico de compras e não em outros fatores                
(ex.: sazonalidade, preferências externas).

# 3. Especificação Técnica

## 3.1. Requisitos de Software
Lista de Requisitos  
● RF01: O sistema deve permitir o cadastro de clientes.  
● RF02: O sistema deve permitir o registro de vendas.  
● RF03: O sistema deve gerar relatórios e gráficos das vendas  
● RF04: O sistema deve fornecer recomendações de produtos para os vendedores 
com base nas vendas anteriores.  
Requisitos Não-Funcionais  
● RNF01: O sistema deve ter uma interface intuitiva e responsiva. 
● RNF02: O sistema deve garantir a segurança dos dados dos clientes. 
● RNF03: O sistema deve processar recomendações de forma eficiente.

## 3.2. Considerações de Design
● Uso de arquitetura MVC (Model-View-Controller).  
● Repositório de dados estruturados para melhorar o desempenho.

## 3.3. Stack Tecnológica
• Linguagens: Python e PHP

• Framework Backend: Adianti Framework

• Banco de Dados: MySQL, com base de dados relacional construída a partir de um dataset público de produtos eletrônicos da Amazon (Amazon Electronics Dataset - Kaggle)

• Front-end: HTML, CSS e JavaScript

• Machine Learning: Scikit-learn, para implementação do módulo de recomendação baseado no histórico de compras dos clientes

### 3.4. Considerações de Segurança

O sistema de gestão de vendas com recomendação inteligente exige atenção especial à segurança dos dados, especialmente por lidar com informações de clientes e históricos de compra. A seguir, são descritas as principais considerações e medidas de segurança adotadas:

• Criptografia de dados sensíveis: Informações pessoais dos clientes (como nome, e-mail) serão armazenadas com criptografia no banco de dados para garantir confidencialidade.

• Autenticação e autorização: O sistema contará com controle de acesso baseado em perfis de usuário (admin, vendedor), utilizando autenticação segura por login e senha.

• Backup e recuperação: Implementação de backups automáticos periódicos do banco de dados para garantir recuperação em caso de falha.

• Proteção contra vazamento de dados: Conformidade com boas práticas de segurança e, quando aplicável, princípios da LGPD (Lei Geral de Proteção de Dados).

## 4. Próximos Passos

A seguir, estão os principais marcos previstos para a execução do projeto durante os Portfólios I e II:

Portfólio I (2025/1): Documentação

• Definição do escopo e requisitos do sistema
• Criação dos diagramas de classes, casos de uso e atividades
• Estruturação da arquitetura (MVC) e escolha da stack
• Conclusão da documentação técnica (RFC)

Portfólio II (2025/2): Desenvolvimento

• Agosto: Implementação das telas de cadastro e histórico

• Setembro: Integração do módulo de recomendação e geração de relatórios

• Outubro: Implementação da segurança e realização de testes

• Novembro: Finalização do sistema e entrega com documentação atualizada

• Dezembro: Apresentação do projeto


## 5. Referências

• Adianti Framework. Disponível em: https://adiantiframework.com.br/


• MySQL Documentation. Disponível em: https://dev.mysql.com/doc/


• Scikit-learn: Machine Learning in Python. Disponível em: https://scikit-learn.org/


• Kaggle Datasets - Amazon Electronics Products. Disponível em: https://www.kaggle.com/datasets


• LGPD - Lei Geral de Proteção de Dados. Lei nº 13.709/2018.

## 6. Avaliações de Professores

Considerações Professor/a: Vanessa Gil - 15/05/25

Considerações Professor/a: Andrei Carniel - 30/05/25

Considerações Professor/a: Leonardo Vitazik Neto - 02/06/25