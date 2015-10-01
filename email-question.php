<?php

    require_once('config.php');
    $errors = false;
    $errormsg = '';
    
    if (isset($_GET['product'])) {
        $product = htmlspecialchars($_GET['product']);
    }
    if (isset($_GET['productid'])) {
        $productid = htmlspecialchars($_GET['productid']);
    }
    
    if (isset($_POST['submit'])) {
        
        $name = preg_replace("/[^a-zA-Z0-9\.]/", "", $_POST['Name']);
        $email = htmlspecialchars($_POST['Email']);
        $question = htmlspecialchars($_POST['Question']);
        $product = htmlspecialchars($_POST['Product']);
        $productid = htmlspecialchars($_POST['Product-ID']);
        
        if ($name == '') {
            $errormsg .= "Please enter your name.<br>";
            $errors = true;
        }
        
        if (!preg_match("/.+\@.+\..+/",$email)) {
            $errormsg .= "Your email address does not appear to be valid.<br>";
            $errors = true;
        }
        
        if ($question == '') {
            $errormsg .= "Please enter your question.<br>";
            $errors = true;
        }
        
        if ($errors == false) {
            $message  = "A question has been submitted about a product. Details below:\r\n" .
                        "From: $name \r\n" .
                        "Email: $email \r\n" . 
                        "Product: $product ($productid) \r\n" .
                        "Question: $question \r\n";
            $to       = "sales@partspros.com";
            $subject  = "Question received about $product";
            $headers  = 'From: PartsPros.com <info@partspros.com>' . "\r\n" .
                "Reply-To: $email" . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
        }
    }; 
?>

<html>

    <head>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700|Bevan|Open+Sans:400,600,600italic,700,700italic,400italic|Cabin:400,400italic,700,700italic" rel="stylesheet" type="text/css" />
        <style type="text/css">
            body {font: 16px 'Open Sans', arial, sans-serif;}
            label {width: 100px; display: inline-block; *display: inline; *zoom: 1; vertical-align: top; margin-bottom: 15px;}
            textarea {width: 340px; height: 80px; margin-bottom: 15px; border: 1px solid #ccc;}
            input.itext {border: 1px solid #ccc; font: 14px 'Open Sans', arial, sans-serif; padding: 3px 5px; width: 300px;}
            .errors {background: #f89f9f; border: 1px solid #f00; border-radius: 10px; padding: 5px 10px 5px;}
        </style>
    </head>
    <body>
    <?php if (isset($_POST['submit']) && $errors == false) { ?>
        
        <p>Your question has been submitted.</p>
        
    <?php } else { ?>
    
        <?php if ($errormsg != '') { echo "<div style=\"margin-bottom: 15px;\" class=\"errors\">$errormsg</div>"; } ?>
        <p>Enter your name, email, and question below, and we'll forward it to our experts.</p>
        <p>Product: <strong><?php echo "$product"; ?></strong></p>
        <form id="qaForm" action="email-question.php" method="post">
            <label for="tName">Name</label><input class="itext" type="text" id="tName" name="Name" /><br>
            <label for="tEmail">Email</label><input class="itext" type="text" id="tEmail" name="Email" /><br>
            <label for="tQuestion">Question:</label><textarea id="tQuestion" name="Question"></textarea><br>
            <input type="Submit" value="Submit" style="margin-left: 100px;"/>
            <input type="hidden" name="Product" value="<?php echo $product;?>" />
            <input type="hidden" name="Product-ID" value="<?php echo $productid;?>" />
            <input type="hidden" name="submit" value="true" />
        </form>
        
    <?php } ?>
    </body>
</html>
