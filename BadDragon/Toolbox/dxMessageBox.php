<dialog id="dxMessageBox">
    <table style="width:<?= empty($dxMessageBoxWidth) ? '350px' : $dxMessageBoxWidth ?>;">
        <tr>
            <td id="dxMessageBoxTitle" style="font-weight: bold;"></td>
            <td style="width:50px;text-align:right;">
                <img class="fa5button" src="<?= BASE_URL ?>public/fa5/window-close.png" alt="close" onclick="javascript:e$('dxMessageBox').close();">
            </td>
        </tr>
        <tr>
            <td id="dxMessageBoxBody" colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right;">
                <button class="button-18" onclick="javascript:e$('dxMessageBox').close();">Close</button>
            </td>
        </tr>
    </table>
</dialog>
<script>
    function showMessageBox(title, body) {
        e$('dxMessageBoxTitle').innerHTML = title
        e$('dxMessageBoxBody').innerHTML = body
        e$("dxMessageBox").showModal()
    }
</script>