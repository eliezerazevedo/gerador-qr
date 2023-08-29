<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">QR Code Generator</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Início</a>
            </li>
            <?php
            // Verifica se o usuário possui o status de "usuário master"
            $isMasterUser = true; // Substitua por sua lógica de verificação real

            // Se for um usuário master, exibe o link "Cadastrar usuário"
            if ($isMasterUser) {
                echo '
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Cadastrar usuário</a>
                    </li>
                ';
            }
            ?>
            <li class="nav-item">
                <a class="nav-link" href="#">Alterar Senha</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Sair</a>
            </li>
        </ul>
    </div>
</nav>