<?php
$dxMessageBoxWidth = empty($dxMessageBoxWidth)? '300px': $dxMessageBoxWidth;
?>
<dialog id="dxMessageBox">
    <table style="width:<?= $dxMessageBoxWidth ?>;">
        <tr>
            <td id="dxMessageBoxTitle" style="font-weight: bold;"></td>
            <td style="width:50px;text-align:right;">
                <img class="fa5button" src="<?= BASE_URL ?>public/fa5/window-close.png" alt="close" onclick="dxMessageBoxClose()">
            </td>
        </tr>
        <tr>
            <td id="dxMessageBoxBody" colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right;">
                <button class="button-18" onclick="dxMessageBoxClose()">Close</button>
            </td>
        </tr>
    </table>
</dialog>
<script>
    function dxMessageBoxClose(){
        e$("dxMessageBox").close()
    }
</script>
