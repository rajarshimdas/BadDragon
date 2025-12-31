<div class="contentbox" style="text-align: center;">
    <a class="button-18" href="<?= BASE_URL ?>home">Home</a>
    <a class="button-18" href="<?= BASE_URL ?>pricing">Pricing</a>
    <a class="button-18 button-18offer" href="<?= BASE_URL ?>offers">2026 Offers</a>
</div>
<style>
    #starttrial {
        background: #1B4F7A;
        padding: 24px 32px;
        border-radius: 10px;
        max-width: 550px;
        margin: 24px auto;
    }

    #trial tr td {
        color: white;
    }
</style>
<div id='starttrial'>

    <table id="trial">
        <tr>
            <td></td>
            <td style="text-align:center;"><b>Get a free Trial</b></td>
        </tr>
        <tr>
            <td style="width: 80px;">Your Name</td>
            <td>
                <input type="text" name="name" id="name">
            </td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <input type="email" name="email" id="email">
            </td>
        </tr>
        <tr>
            <td>Website</td>
            <td>
                <input type="url" name="website" id="website">
            </td>
        </tr>
        <tr>
            <td></td>
            <td id="dxButton" style="text-align:center;">
                <button class="button-18" onclick="startMyTrial()">Start Trial</button>
            </td>
        </tr>
    </table>

</div>

<?php require_once BD . '/Toolbox/dxMessageBox.php'; ?>

<script>
    const dxTrial = e$('dxMessageBox')

    function startMyTrial() {

        let name = e$('name').value
        let email = e$('email').value
        let website = e$('website').value

        var formData = new FormData()

        formData.append("a", "w3-portal-starttrial")
        formData.append("name", name)
        formData.append("email", email)
        formData.append("website", website)

        bdFetchAPI(apiUrl, formData).then((response) => {
            console.log(response);

            if (response[0] != "T") {
                e$("dxMessageBoxTitle").innerHTML = "Trial"
                e$("dxMessageBoxBody").innerHTML = response[1]
                dxTrial.showModal()
            } else {
                e$("starttrial").innerHTML = response[1]
            }
        });

    }
</script>