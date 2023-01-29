/** Get form data and return an object
@param  {HTMLFormControlsCollection} elements
@return {Object}
*/
const formToJSON = elements => [].reduce.call(elements, (data, element) => 
{
    if(element.name!="")
    {
        data[element.name] = element.value;
    }
  return data;
}, {});
 
function changeUserData()
{
    let url = "/AutoRecipe/user_data?nickname=" + getCookie("nickname");
    const data = formToJSON(document.getElementById("form").elements);     // convert form data into object
    let JSONdata = JSON.stringify(data, null, "  ");	// convert object into a JSON string
    
    let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
    xmlhttp.open("PUT", url, true);   // Set method, URL of the request
    xmlhttp.setRequestHeader("Accept", "application/json");
    xmlhttp.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
     
    xmlhttp.onreadystatechange = function()    // Code to run depending on the response
    {
        if (this.readyState == 4 && this.status == 201)    // OK
        {
            if(data.hasOwnProperty("nickname")) {
                setCookie("nickname", data["nickname"], 1);
                var response = JSON.parse(xmlhttp.responseText);
                setCookie("token", response["token"], 1);
            }
            if(data.hasOwnProperty("password")) {
                var response = JSON.parse(xmlhttp.responseText);
                setCookie("token", response["token"], 1);
            }
            getUserData();
        }
        if(this.readyState == 4 && this.status != 201)     // not OK
        {
            console.log(this.status);
        }
    };
     
    xmlhttp.send(JSONdata);
}