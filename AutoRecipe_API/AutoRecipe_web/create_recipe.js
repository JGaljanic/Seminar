function createRecipe() {

    let fragment = document.createDocumentFragment();

    // Create a div element for new recipe name
    let div = document.createElement("div");

    // Create the input element for the new recipe name
    let input = document.createElement("input");
    input.id = "new_name";
    input.type = "text";
    input.required;

    // Create the input button to save the new recipe name
    let input_button = document.createElement("button");
    input_button.id = "input_button";
    input_button.innerHTML = "Submit";
    input_button.onclick = function () {
        var recipe_data_object = {recipe_name: document.getElementById("new_name").value, ingredients: {ingredient1: "Enter your first ingredient"}, data: {description: "Enter description", step1: {step1_text: "Enter the first step", step1_picture: ""}}, added_by: getCookie("nickname")};
        sendNewRecipe(recipe_data_object);
    }
    div.appendChild(input);
    div.appendChild(input_button);

    // Add the completed div to fragment
    fragment.appendChild(div);
    document.getElementById("new_recipe_inputs").appendChild(fragment);
}

function sendNewRecipe(recipe_data_object) {
    let url = "/AutoRecipe/recipes";
    let JSONdata = JSON.stringify(recipe_data_object, null, "  ");	// convert object into a JSON string
    
    let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
    xmlhttp.open("POST", url, true);   // Set method, URL of the request
    xmlhttp.setRequestHeader("Accept", "application/json");
    xmlhttp.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
     
    xmlhttp.onreadystatechange = function()    // Code to run depending on the response
    {
        if (this.readyState == 4 && this.status == 200)    // OK
        {
            // Remove the input field and input button
            var input_field = document.getElementById("new_name");
            input_field.remove();
            let input_button = document.getElementById("input_button");
            input_button.remove();
            getRecipeList();
        }
        if(this.readyState == 4 && this.status != 200)     // not OK
        {
            console.log(this.status);
        }
    };

    xmlhttp.send(JSONdata);
}