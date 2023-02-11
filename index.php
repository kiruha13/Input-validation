
<?php
include "db.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["text"] != '') {
        $text = $_POST["text"];
        $text_length = mb_strlen($text);
        $russian_chars = 0;
        $english_chars = 0;
        for ($i = 0; $i < $text_length; $i++) {
            $char = mb_substr($text, $i, 1, "utf8");
            if (($char >= 'А' && $char <= 'я') || $char == 'ё') {
                $russian_chars++;
            } elseif (($char >= 'A' && $char <= 'z') || ($char >= 'a' && $char <= 'z')) {
                $english_chars++;
            }
        }
        if ($russian_chars >= $english_chars) {
            $lang = "Russian";
        } else {
            $lang = "English";
        }
        $result = "";
        for ($i = 0; $i < $text_length; $i++) {
            $char = mb_substr($text, $i, 1, "utf8");
            if ((($char >= 'А' && $char <= 'я') || $char == 'ё') && $lang == "English") {
                $result .= "<span class='highlight'>$char</span>";
            } elseif ((($char >= 'A' && $char <= 'z') || ($char >= 'a' && $char <= 'z')) && $lang == "Russian") {
                $result .= "<span class='highlight'>$char</span>";
            } else {
                $result .= $char;
            }
        }
        $result = htmlspecialchars($result);
        // Save the result to the database
        $sql = "INSERT INTO history (text, result) VALUES ('$text', '$result')";
        if ($db->query($sql) === TRUE) {
            echo "Success!";
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }

    }
    else{
        echo "<p class='error'> Error: Field is empty</p>";
    }
}

?>
<html>
<head>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<form id="form" method="post">
    <textarea id="text" name="text"><?php if (!($text==$result)) echo $text ?? ''; ?></textarea>
    <br>
    <input id="submit" type="submit" value="Check">
</form>
<!-- Display the verification history -->
<h2 align="center">Verification History</h2>
<table class="history">
    <tr>
        <th>Text</th>
        <th>Result</th>
    </tr>
<?php

$sql = "SELECT text, result FROM history ORDER BY id desc";
$result = $db->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["text"] . "</td>";
        echo "<td>" . htmlspecialchars_decode($row["result"]) . "</td>";
        echo "</tr>";
    }
}

?>
</table>
</body>
</html>
