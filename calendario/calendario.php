<?php
//==============================
// CONEXÃO
//==============================
$pdo = new mysqli("localhost", "root", "", "MODELO_TCC");

if($pdo->connect_error){
    die("Erro: ".$pdo->connect_error);
}

//==============================
// CADASTRAR EVENTO
//==============================
if(isset($_POST['salvar'])){

    $nome = $_POST['nome'];
    $descr = $_POST['descr'];
    $data = $_POST['data'];
    $tipo = $_POST['tipo'];

    $sql = $pdo->prepare("INSERT INTO eventos(nome, descr, data_evento, tipo)
                          VALUES(?,?,?,?)");

    $sql->bind_param("ssss",$nome,$descr,$data,$tipo);
    $sql->execute();
}

//==============================
// BUSCAR EVENTOS
//==============================
$eventos = [];

$sql = $pdo->query("SELECT * FROM eventos");

while($dados = $sql->fetch_assoc()){

    $eventos[$dados['data_evento']][] = $dados;

}

//==============================
// CALENDÁRIO
//==============================
$mes = isset($_GET['mes']) ? $_GET['mes'] : date("m");
$ano = isset($_GET['ano']) ? $_GET['ano'] : date("Y");

$diasMes = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);

$primeiroDia = date("w",strtotime("$ano-$mes-01"));

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<link rel="stylesheet" href="css_calendario.css">
<meta charset="UTF-8">

<title>Calendário Escolar</title>

</head>
<body>

<h2>Calendário Escolar</h2>

<form method="POST">

<input type="text" name="nome" placeholder="Nome do evento" required>

<br>

<textarea name="descr" placeholder="Descrição"></textarea>

<br>

<input type="date" name="data" required>

<select name="tipo">

<option>Prova</option>
<option>Seminário</option>
<option>Atividade</option>
<option>Evento</option>
<option>Palestra</option>

</select>

<button name="salvar">Cadastrar Evento</button>

</form>

<h3><?php echo "$mes / $ano"; ?></h3>

<table>

<tr>

<th>Dom</th>
<th>Seg</th>
<th>Ter</th>
<th>Qua</th>
<th>Qui</th>
<th>Sex</th>
<th>Sáb</th>

</tr>

<tr>

<?php

for($i=0;$i<$primeiroDia;$i++){

    echo "<td></td>";

}

$contador = $primeiroDia;

for($dia=1;$dia<=$diasMes;$dia++){

    $dataAtual = sprintf("%04d-%02d-%02d",$ano,$mes,$dia);

    echo "<td>";

    echo "<strong>$dia</strong>";

    if(isset($eventos[$dataAtual])){

        foreach($eventos[$dataAtual] as $evento){

            echo "<div class='evento'>";

            echo "<b>".$evento['nome']."</b><br>";

            echo $evento['tipo']."<br>";

            echo $evento['descr'];

            echo "</div>";

        }

    }

    echo "</td>";

    $contador++;

    if($contador % 7 == 0){

        echo "</tr><tr>";

    }

}

?>

</tr>

</table>

</body>
</html>

