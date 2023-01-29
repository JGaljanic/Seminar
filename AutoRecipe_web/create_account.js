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
  
 function createAccount()
 {
     const data = formToJSON(document.getElementById("form").elements);     // convert form data into object
     let JSONdata = JSON.stringify(data, null, "  ");	// convert object into a JSON string
     
     let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
      
     xmlhttp.onreadystatechange = function()    // Code to run depending on the response
     {
         if (this.readyState == 4 && this.status == 201)    // OK
         {
            window.location.replace("login_page.html");
         }
         if(this.readyState == 4 && this.status != 201)     // not OK
         {
             console.log(this.status);
         }
     };
      
     xmlhttp.open("POST", "/AutoRecipe/user_data.php", true);   // Set method, URL of the request
     xmlhttp.send(JSONdata);
 }