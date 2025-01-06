<div class="contentbox" style="text-align: center;">
    <a class="button-18" href="<?= BASE_URL ?>home">Arkafe</a>
    <a class="button-18" href="<?= BASE_URL ?>pricing">Pricing</a>
</div>

<!-- Todo | Data validation error messages -->
<dialog id="dxTrial">
    <table style="width:300px;">
        <tr>
            <td class='rd-text-bold'>Trial</td>
            <td style="width:50px;text-align:right;">
                <img class="fa5button" src="/images/fa5/close.png" alt="close" onclick="dxTrialClose()">
            </td>
        </tr>
        <tr>
            <td id="dxMessage" colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right;">
                <button class="button-18" onclick="dxTrialClose()">Close</button>
            </td>
        </tr>
    </table>
</dialog>

<style>
    table tr td {
        color: var(--rd-dark-gray);
    }

    #trial {
        width: 350px;
        margin: auto;
        border-spacing: 6px;
    }

    #trial tr td:first-child {
        text-align: right;
    }

    input {
        width: 100%;
        padding: 4px;
    }
</style>

<div class="contentBox" style="background-color: white; color: var(--rd-dark-gray);padding:30px 5px; height: 180px;">
    <div id='starttrial' style="text-align: center; width:350px; margin: auto;">
        
        <table id="trial">
            <tr>
                <td></td>
                <td><b>Get a free Trial</b></td>
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
</div>

<script>
    
    const dxTrial = e$('dxTrial')

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
                e$("dxMessage").innerHTML = response[1]
                dxTrial.showModal()
            } else {
                e$("starttrial").innerHTML = response[1]
            }
        });

    }

    function dxTrialClose() {
        dxTrial.close()
    }

</script>