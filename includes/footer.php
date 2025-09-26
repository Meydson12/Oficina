    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-fechar alerts após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Focar no campo de busca ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const searchField = document.querySelector('input[type="search"]');
            if (searchField) {
                searchField.focus();
            }
        });
    </script>
    </body>

    </html>