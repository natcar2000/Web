<?php

function valida_dados(){
    $p_nome = $_POST['p_nome'];
    $u_nome = $_POST['u_nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $c_senha = $_POST['c_senha'];

    if ($p_nome == "" || $u_nome == "" || $email == "" || $senha == "" || $c_senha == ""){
        return "Nenhum campo pode ficar em branco";
    }
    else if (!preg_match("/^[a-zA-Z-' ]*$/", $p_nome) || !preg_match("/^[a-zA-Z-' ]*$/", $u_nome)){
        return "Insira apenas letras nos campos de nome";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return "Formato de email incorreto";
    }
    else if ($senha != $c_senha){
        return "As senhas devem ser iguais";
    }
    else if (strlen($p_nome) > 15 || strlen($u_nome) > 15 ){
        return "Campos de nome devem ter 15 caracteres ou menos";
    }
    else if (strlen($email) > 100){
        return "Campo de email deve ter 100 caracteres ou menos";
    }
    else if (strlen($senha) < 8 || strlen($senha) > 16 || strlen($c_senha) < 8 || strlen($c_senha) > 16){
        return "As senhas devem ter entre 8 e 16 caracteres";
    }
    else {
        $dados_do_usuario = array('p_nome'=>$p_nome, 'u_nome'=>$u_nome, 'email'=>$email, 'senha'=>$senha);
        return $dados_do_usuario; 
    }
}


function valida_credenciais(){
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if ($email == "" || $senha == ""){
        return "Nenhum campo pode ficar em branco";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return "Formato de email incorreto";
    }
    else if (strlen($senha) < 8 || strlen($senha) > 16){
        return "Sua senha tem entre 8 e 16 caracteres";
    }
    else{
        $credenciais_do_usuario = array('email'=>$email, 'senha'=>$senha);
        return $credenciais_do_usuario; 
    }
}


function higieniza_entrada($entrada){
    $entrada = trim($entrada);
    $entrada = stripslashes($entrada);
    $entrada = htmlspecialchars($entrada);
    return $entrada;
}


$action = $_POST['action'];

$conn = new PDO("mysql:host=localhost; dbname=projetos", "natan", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

if ($action == 'registro'){
    $dados_do_usuario = valida_dados();
    $p_nome = higieniza_entrada($dados_do_usuario['p_nome']);
    $u_nome = higieniza_entrada($dados_do_usuario['u_nome']);
    $email = higieniza_entrada($dados_do_usuario['email']);
    $senha = higieniza_entrada($dados_do_usuario['senha']);
    $registro = $conn->prepare("INSERT INTO usuarios (nome, sobrenome, email, senha) VALUES (:p_nome, :u_nome, :email, :senha)");
    $registro->execute(array('p_nome'=>$p_nome, 'u_nome'=>$u_nome, 'email'=>$email, 'senha'=>$senha));
    header('Location: login.html');    
}
else if ($action == 'login'){
    $credenciais_do_usuario = valida_credenciais();
    $email = higieniza_entrada($credenciais_do_usuario['email']);
    $senha = higieniza_entrada($credenciais_do_usuario['senha']);
    $consulta = $conn->prepare("SELECT * FROM usuarios");
    $consulta->execute();
    while ($row = $consulta->fetch()){
        if ($email == $row['email'] && $senha == $row['senha']){
            $campo_de_nome = $row['nome'];
            $campo_de_sobrenome = $row['sobrenome'];
            $campo_de_email = $row['email'];
            echo '<br>';
            echo '<p>Nome: '.$campo_de_nome.'</p>';
            echo '<p>Sobrenome: '.$campo_de_sobrenome.'</p>';
            echo '<p>Email: '.$campo_de_email.'</p>';
        }
        else{
            echo "Senha ou usuario invalido";
        }
    }
}
else{
    echo "Erro! Informe um 'action' correto";    
}

?>
