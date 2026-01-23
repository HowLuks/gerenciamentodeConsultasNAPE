<?php
function sanitizarDados($dado)
{
    // Remove tags HTML
    $dado = strip_tags($dado);  

    // Remove espaços extras no início/fim
    $dado = trim($dado);

    return $dado; // Retorna o valor sanitizado
}

sanitizarDados($dado);
