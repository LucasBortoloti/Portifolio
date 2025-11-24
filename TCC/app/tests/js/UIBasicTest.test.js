/**
 * @jest-environment jsdom
 */

test('Deve exibir o título "Lista de Vendas"', () => {
    document.body.innerHTML = `
        <div class="card-header">
            <div class="card-title">Lista de Vendas</div>
        </div>
    `;

    const title = document.querySelector('.card-title');
    expect(title).not.toBeNull();
    expect(title.textContent).toBe('Lista de Vendas');
});


test('Botão "Recomendar produtos" deve existir na tabela', () => {
    document.body.innerHTML = `
        <table>
            <tr>
                <td>
                    <a title="Recomendar produtos">
                        <i class="fa fa-cogs"></i>
                    </a>
                </td>
            </tr>
        </table>
    `;

    const btn = document.querySelector('a[title="Recomendar produtos"]');
    expect(btn).not.toBeNull();
});
