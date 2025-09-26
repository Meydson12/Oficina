<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-tools"></i> GenAuto Oficina
        </a>
        
        <div class="d-flex align-items-center">
            <span class="text-light me-3">
                <i class="bi bi-person-circle"></i> <?php echo $_SESSION['usuario_nome']; ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm" 
               onclick="return confirm('Tem certeza que deseja sair?')">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>
    </div>
</nav>