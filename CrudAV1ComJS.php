<?php
$arquivo = 'Perguntas.txt';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'];


    if ($acao == 'create' && isset($_POST['pergunta'])) {
        $pergunta = $_POST['pergunta'];


        $id = 1;
        if (file_exists($arquivo)) {
            $perguntas = file($arquivo);
            $ultimoId = explode(';', end($perguntas))[0];
            $id = intval($ultimoId) + 1;
        }

        $novaPergunta = $id . ';' . $pergunta . PHP_EOL;
        file_put_contents($arquivo, $novaPergunta, FILE_APPEND);
        echo "Pergunta adicionada com sucesso!";


    } elseif ($acao == 'update' && isset($_POST['id']) && isset($_POST['pergunta'])) {
        $id = $_POST['id'];
        $novaPergunta = $_POST['pergunta'];

        if (file_exists($arquivo)) {
            $perguntas = file($arquivo);
            $encontrado = false;

            foreach ($perguntas as &$linha) {
                $exp = explode(';', $linha);
                if (isset($exp[0]) && intval($exp[0]) == $id) {
                    $linha = $id . ';' . $novaPergunta . PHP_EOL;
                    $encontrado = true;
                    break;
                }
            }

            if ($encontrado) {
                file_put_contents($arquivo, implode('', $perguntas));
                echo "Pergunta atualizada com sucesso!";
            } else {
                echo "Pergunta com ID $id não encontrada.";
            }
        } else {
            echo "Arquivo não encontrado!";
        }


    } elseif ($acao == 'delete' && isset($_POST['id'])) {
        $id = $_POST['id'];

        if (file_exists($arquivo)) {
            $perguntas = file($arquivo);
            $novasPerguntas = [];
            $deletado = false;

            foreach ($perguntas as $linha) {
                $exp = explode(';', $linha);
                if (isset($exp[0]) && intval($exp[0]) == $id) {
                    $deletado = true;
                } else {
                    $novasPerguntas[] = $linha;
                }
            }

            file_put_contents($arquivo, implode('', $novasPerguntas));
            echo $deletado ? "Pergunta deletada com sucesso!" : "Pergunta com ID $id não encontrada.";
        } else {
            echo "Arquivo não encontrado!";
        }
    }

} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['acao']) && $_GET['acao'] == 'read') {

    if (file_exists($arquivo)) {
        $perguntas = file($arquivo);
        $listaPerguntas = [];

        foreach ($perguntas as $linha) {
            $exp = explode(';', trim($linha));
            if (isset($exp[0]) && isset($exp[1])) {
                $listaPerguntas[] = ['id' => $exp[0], 'texto' => $exp[1]];
            }
        }

        echo json_encode($listaPerguntas);
    } else {
        echo json_encode([]);
    }
}
?>
