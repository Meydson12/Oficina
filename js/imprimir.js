function imprimirRecibo() {
    // Criar uma nova janela para impressão
    const janelaImpressao = window.open('', '_blank');
    
    // Pegar o HTML do recibo
    const reciboHTML = document.querySelector('.recibo-elegante').outerHTML;
    
    // CSS para impressão
    const cssImpressao = `
        <style>
            @media print {
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                .recibo-elegante { 
                    max-width: 100% !important; 
                    box-shadow: none !important;
                    border: 1px solid #ddd !important;
                }
                .btn, .d-print-none { display: none !important; }
                .bg-success, .bg-primary { -webkit-print-color-adjust: exact; }
            }
            @page { margin: 0; }
        </style>
    `;
    
    // Montar o documento completo para impressão
    janelaImpressao.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Recibo Venda #<?php echo $venda_id; ?></title>
            ${cssImpressao}
        </head>
        <body>
            ${reciboHTML}
            <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
                Recibo gerado em <?php echo date('d/m/Y H:i'); ?> - GenAuto Sistema
            </div>
        </body>
        </html>
    `);
    
    janelaImpressao.document.close();
    janelaImpressao.print();
    janelaImpressao.close();
}