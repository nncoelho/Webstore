<?php
// Imprime tudo o que encontrar na sessão
session_start();
echo '<pre>';
print_r($_SESSION);
