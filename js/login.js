function logar() {
    var login = document.getElementById('login').value;
    var senha = document.getElementById('senha').value;

    if (login === "teste" && senha === "1234") {
        location.href = "dashboard.php";
    } else {
        alert('usuario não autorizado');
    }
}


document.addEventListener('DOMContentLoaded', () => {
    const mode = document.getElementById('mode_icon');
    const form = document.getElementById('login_form');

    console.log('DOM pronto. mode:', mode, 'form:', form);

    if (!mode) {
        console.error('Elemento #mode_icon não encontrado. Verifique o id no HTML.');
        return;
    }
    if (!form) {
        console.error('Elemento #login_form não encontrado. Verifique o id no HTML.');
        return;
    }

    // função para alternar o ícone e o tema
    function toggleTheme() {
        console.log('Clique detectado no ícone de modo.');

        // caso o ícone tenha a classe fa-moon -> mudar para sun
        if (mode.classList.contains('fa-moon')) {
            mode.classList.remove('fa-moon');
            mode.classList.add('fa-sun');
            form.classList.add('dark');
            mode.setAttribute('aria-label', 'Modo claro ativado');
            console.log('Modo escuro ativado (classe dark adicionada).');
            return;
        }

        // senão reverte para lua
        mode.classList.remove('fa-sun');
        mode.classList.add('fa-moon');
        form.classList.remove('dark');
        mode.setAttribute('aria-label', 'Modo escuro ativado');
        console.log('Modo claro ativado (classe dark removida).');
    }

    // ligar o evento
    mode.addEventListener('click', toggleTheme);
});

