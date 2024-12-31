/*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   11-May-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/


/*
+---------------------------------------------------------------------------+
| https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch    |
+---------------------------------------------------------------------------+
| Example POST method implementation:                                       |
+---------------------------------------------------------------------------+
*/
async function bdPostData(url = "", formData = {}) {
    // Default options are marked with *
    const response = await fetch(url, {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        credentials: "same-origin", // include, *same-origin, omit
        // headers: {
        // "Content-Type": "application/json",
        // 'Content-Type': 'application/x-www-form-urlencoded',
        // },
        redirect: "error", // manual, *follow, error
        referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        //body: JSON.stringify(data), // body data type must match "Content-Type" header
        body: formData, // RD - use FormData
    });
    return response.json(); // parses JSON response into native JavaScript objects
}

/* Howto use bdPostData function
+---------------------------------------------------------------------------+
| https://developer.mozilla.org/en-US/docs/Web/API/FormData                 |
+---------------------------------------------------------------------------+

const apiUrl = "<?= $base_url ?>index.cgi";
var formData = new FormData()
formData.append("a", "tasks-api-fetchProjectData")

bdPostData(apiUrl,formData).then((response) => {
    console.log(response);
});
*/


function e$(eid){
    return document.getElementById(eid)
}
