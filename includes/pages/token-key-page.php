<div class="woocommerce" style="padding-left: 20px; padding-right: 20px;">
    <h2>Token Key Details</h2>
    
    <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['token_key'])) {
            $token = sk_generate_random_key(50);
            if (update_option('sk_app_token_key', $token)) { ?>
                <div id="message" class="updated inline" style="margin-left: 0;">
		            <p><strong>Key generated successfully. Make sure to keep your key saved, and do not share with untrusted parties.</strong></p>
	            </div>
            <?php } else { ?>
                <div id="message" class="updated inline" style="margin-left: 0;">
		            <p><strong>Unable to generate new key.</strong></p>
	            </div>
            <?php }
        }
    ?>

    

    <form name="tokenForm" action="" method="POST" onsubmit="return skValidateTokenForm()">
        <table class="form-table">
            <tbody>
            <?php if (!empty(get_option("sk_app_token_key", ""))) { ?>
            <tr valign="top">
                    <th scope="row" class="titledesc">
                        QRCode				</th>
                    <td class="forminp">
                        <div id="keys-qrcode"></div>
                    </td>
            </tr>
            <?php } ?>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        Key				</th>
                    <td class="forminp">
                        <input id="token_key" name="token_key" type="text" value="<?php echo get_option("sk_app_token_key", ""); ?>" size="55" placeholder="Key"">
                        <button type="submit" class="button-secondary copy-key" data-tip="Copied!">Generate new key</button>
                    </td>
                </tr>
                
            </tbody>
        </table>

        <p>
    This security token provides authentication for accessing your entire wodpress app api through any device that use the key generated below.
    <br><br>
    <b>Risk: </b> Don't share your key to untrusted parties, Remove/Gitignore the token in your app source code before sharing.
    </p>

    
    </form>


</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
    jQuery(document).ready(function() {
        var x = document.getElementById("token_key").value;
        $('#keys-qrcode').html('').qrcode({width: 200, height: 200, text: x});
    });
</script>
<script>
    function skValidateTokenForm() {
        let token = document.forms["tokenForm"]["token_key"].value;
        if (token.length < 5) { //empty key
            return true;
        } else {
            if (confirm("Are you sure? You want to create new key, old key will be trashed.") == true) {
                return true;
            } else {
                return false;
            }
        }
    }
</script>