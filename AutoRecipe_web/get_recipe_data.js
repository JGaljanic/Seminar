function getRecipeData() {
    let recipe_ID = getCookie("clicked_recipe_ID");       // Get the currently logged in user from a cookie
    let xmlhttp1 = new XMLHttpRequest();                // Create an empty HTTP request to get the ingredients (their relations to the recipe are in a separate table)
    let xmlhttp2 = new XMLHttpRequest();                // Create an empty HTTP request to get the recipe data (description + steps + step picture paths)
    let xmlhttp3 = new XMLHttpRequest();                // Create an empty HTTP request to get the thumbnail
    let url1 = "/AutoRecipe/get_ingredients?recipe_ID=" + recipe_ID;     // Create a URL that'll be sent to the server
    let url2 = "/AutoRecipe/recipes?recipe_ID=" + recipe_ID;     // Create a URL that'll be sent to the server
    let url3 = "/AutoRecipe/get_thumbnail?recipe_ID=" + recipe_ID;
    var recipe_data_object = {new_recipe_name: getCookie("clicked_recipe_name"), ingredients: {}, data: {}};    // Create an object that'll get edited when the recipe gets edited, then it gets converted into JSON and sent to the server where the changes are applied

    document.getElementById("recipe_page_header").innerHTML = getCookie("clicked_recipe_name");

    // GET THE INGREDIENTS
    xmlhttp1.onreadystatechange = function ()
    {
        if (this.readyState == 4 && this.status == 200) {       // OK
            try{
				var response = JSON.parse(this.responseText);   // Parse the received text as JSON
                recipe_data_object.ingredients = response;
			}
			catch(e){
				return;
			}
            var fragment = document.createDocumentFragment();		// Create a new fragment to put the created div element into, then attach it to the existing div on the HTML document.
            for(var i = 0; i < Object.keys(response).length; i++) {
                let j = i + 1;

                // Create the div where the ingredient's name and buttons will be
                let div = document.createElement("div");
                //div.innerHTML = response[i]["ingredient_name"];
                div.innerHTML = response["ingredient" + j];

                // Create the button for editing the ingredient
                let buttonEdit = document.createElement("button");
                buttonEdit.innerHTML = "Edit";
                buttonEdit.onclick = function() {
                    // Make the other inputs on the site disappear
                    Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
                        function(item) {
                          item.remove();
                        });
                    // Make the other input buttons on the site disappear
                    if (document.contains(document.getElementById("input_button"))) {
                        document.getElementById("input_button").remove();
                    }
                    // Create the input field for the new name of the ingredient
                    let input = document.createElement("input");
                    input.id = "ingredient" + j;
                    input.type = "text";
                    input.required;
                    // Create the button that will apply the new name of the ingredient
                    let input_button = document.createElement("button");
                    input_button.id = "input_button";
                    input_button.innerHTML = "Submit";
                    input_button.onclick = function () {
                        // Change the recipe object and send it off to be converted to JSON and sent to the server
                        recipe_data_object.ingredients[input.id] = document.getElementById(input.id).value;
                        changeRecipeData(recipe_data_object);
                    }
                    // Append the input field and the button to the ingredient's div
                    div.appendChild(input);
                    div.appendChild(input_button);
                }
                div.appendChild(buttonEdit);
                
                // Create the button for deleting the ingredient
                if(Object.keys(recipe_data_object.ingredients).length > 1) {
                    let buttonDelete = document.createElement("button");
                    buttonDelete.innerHTML = "Delete";
                    buttonDelete.onclick = function() {
                        //delete recipe_data_object.ingredients["ingredient" + j];
                        for(let k = j; k <= Object.keys(recipe_data_object.ingredients).length; k++) {
                            let l = k + 1;
                            if(k == Object.keys(recipe_data_object.ingredients).length) {
                                delete recipe_data_object.ingredients["ingredient" + k];
                                break;
                            }
                            else {
                                recipe_data_object.ingredients["ingredient" + k] = recipe_data_object.ingredients["ingredient" + l];
                            }
                        }
                        changeRecipeData(recipe_data_object);
                    }
                    div.appendChild(buttonDelete);
                }

                // Append the finished div to the fragment
                fragment.appendChild(div);
            }

            // Create the button that'll add a new ingredient
            let div = document.createElement("div");
            let buttonAdd = document.createElement("button");
            buttonAdd.innerHTML = "Add";
            buttonAdd.onclick = function() {
                Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
                    function(item) {
                        item.remove();
                    });
                if (document.contains(document.getElementById("input_button"))) {
                    document.getElementById("input_button").remove();
                }
                let input = document.createElement("input");
                let next_ingredient_id = Object.keys(recipe_data_object.ingredients).length + 1;
                input.id = "ingredient" + next_ingredient_id;
                input.type = "text";
                input.required;
                let input_button = document.createElement("button");
                input_button.id = "input_button";
                input_button.innerHTML = "Submit";
                input_button.onclick = function () {
                    recipe_data_object.ingredients[input.id] = document.getElementById(input.id).value;
                    changeRecipeData(recipe_data_object);
                }
                div.appendChild(input);
                div.appendChild(input_button);
            }
            div.appendChild(buttonAdd);
            fragment.appendChild(div);

            document.getElementById("recipe_ingredients").appendChild(fragment);
        }
    };

    // GET THE REST OF THE RECIPE (DESCRIPTION + STEPS)
    xmlhttp2.onreadystatechange = function ()
    {
        if (this.readyState == 4 && this.status == 200) {       // OK
            try{
				var response = JSON.parse(this.responseText);   // Parse the received text as JSON
                recipe_data_object.data = response;
			}
			catch(e){
                console.log(e);
				return;
			}

            // Go through the JSON formatted data
            let i = 1;
            for(var recipe_data_element in response)	
            {
                // Create the description segment
                if(recipe_data_element == "description") {
                    // Create a fragment for the description
                    let fragment = document.createDocumentFragment();

                    // Create a div element for description, save the text into its innerHTML
                    let div = document.createElement("div");
                    div.innerHTML = response[recipe_data_element];

                    // Create the button for editing of description
                    let buttonEdit = document.createElement("button");
                    buttonEdit.innerHTML = "Edit";
                    buttonEdit.onclick = function() {
                        // Remove other inputs from the page
                        Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
                            function(item) {
                                item.remove();
                            });
                        if (document.contains(document.getElementById("input_button"))) {
                            document.getElementById("input_button").remove();
                        }

                        // Create the input element for the new description
                        let input = document.createElement("input");
                        input.id = "new_description";
                        input.type = "text";
                        input.required;

                        // Create the input button to save the new description
                        let input_button = document.createElement("button");
                        input_button.id = "input_button";
                        input_button.innerHTML = "Submit";
                        input_button.onclick = function () {
                            recipe_data_object.data.description = document.getElementById("new_description").value;
                            changeRecipeData(recipe_data_object);
                        }
                        div.appendChild(input);
                        div.appendChild(input_button);
                    }
                    div.appendChild(buttonEdit);
                    // Add the completed div to fragment
                    fragment.appendChild(div);
                    // Add the completed fragment to the description element on the HTML page
                    document.getElementById("recipe_description").appendChild(fragment);
                }
                // Create the steps segment
                else {
                    // j will save the value of i on each step for element IDs and such
                    let j = i;
                    
                    // Create a new fragment for one step's segment
                    let fragment = document.createDocumentFragment();
                    
                    // Create a div element for one step
                    let div = document.createElement("div");
                    
                    // Create an img element for step's picture
                    let img = document.createElement("img");
                    img.src = "http://localhost/AutoRecipe/" + response[recipe_data_element]["step" + i + "_picture"];
                    img.width = "100";
                    img.height = "100";
                    
                    // Create a p element for step's text
                    let p = document.createElement("p");
                    p.innerHTML = recipe_data_element + ": " + response[recipe_data_element]["step" + i + "_text"];
                    p.style = "display:inline";

                    // Create a span element for the form parts that appear when the button for changing the picture is clicked
                    let spanChangePicture = document.createElement("span");

                    // Create the button that will show the form for changing the picture
                    let buttonChangePicture = document.createElement("button");
                    buttonChangePicture.innerHTML = "Change picture";
                    buttonChangePicture.onclick = function() {
                        // Remove all other input elements on the page
                        Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
                            function(item) {
                                item.remove();
                            });
                        if (document.contains(document.getElementById("input_button"))) {
                            document.getElementById("input_button").remove();
                        }

                        if (document.contains(document.getElementById("uploadform"))) {
                            document.getElementById("uploadform").remove();
                        }

                        // Create the form
                        let change_picture_form = document.createElement("form");
                        change_picture_form.id="uploadform";
                        change_picture_form.style = "display:inline";
                        
                        // Create the input button for chosing the file
                        let inputFile = document.createElement("input");
                        inputFile.type = "file";
                        inputFile.name = "file";

                        // Create the input button for submission of the file
                        let inputSubmit = document.createElement("input");
                        inputSubmit.type = "submit";
                        inputSubmit.value = "Upload Picture";

                        // Add all new elements to the change picture form
                        change_picture_form.appendChild(inputFile);
                        change_picture_form.appendChild(inputSubmit);
                        spanChangePicture.appendChild(change_picture_form);

                        changeStepPicture("/AutoRecipe/recipe_pictures?picture_path=" + recipe_data_object.data["step" + j]["step" + j + "_picture"]);
                    }

                    // Create the button for editing the step's text
                    let buttonEdit = document.createElement("button");
                    buttonEdit.innerHTML = "Edit";
                    buttonEdit.onclick = function() {
                        // Remove all other input elements on the page
                        Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
                            function(item) {
                              item.remove();
                            });
                        if (document.contains(document.getElementById("input_button"))) {
                            document.getElementById("input_button").remove();
                        }

                        // Create the input element where the new step's text will be entered
                        let input = document.createElement("input");
                        input.id = "step" + j + "_text";
                        input.type = "text";
                        input.required;

                        // Create the submit button that'll save the new step's text
                        let input_button = document.createElement("button");
                        input_button.id = "input_button";
                        input_button.innerHTML = "Submit";
                        input_button.onclick = function () {
                            recipe_data_object.data["step" + j][input.id] = document.getElementById(input.id).value;
                            changeRecipeData(recipe_data_object);
                        }
                        // Append the two new elements to the step's div
                        div.appendChild(input);
                        div.appendChild(input_button);
                    }

                    // Append all new elements to the step's div except for the delete button (only gets appended if there's more than 1 step left)
                    div.appendChild(img);
                    div.appendChild(buttonChangePicture);
                    div.appendChild(spanChangePicture);
                    div.appendChild(p);
                    div.appendChild(buttonEdit);

                    // Create the button for step's deletion
                    if(Object.keys(recipe_data_object.data).length > 2) {
                        let buttonDelete = document.createElement("button");
                        buttonDelete.innerHTML = "Delete";
                        buttonDelete.onclick = function() {
                            deleteStepPicture(recipe_data_object.data["step" + j]["step" + j + "_picture"]);
                            let k;
                            for(k = j; k < Object.keys(recipe_data_object.data).length - 1; k++) {
                                let l = k + 1;
                                if(k == Object.keys(recipe_data_object.data).length - 1) {
                                    delete recipe_data_object.data["step" + k];
                                    break;
                                }
                                else {
                                    recipe_data_object.data["step" + k]["step" + k + "_text"] = recipe_data_object.data["step" + l]["step" + l + "_text"];
                                }
                            }
                            delete recipe_data_object.data["step" + k];
                            changeRecipeData(recipe_data_object);
                        }
                        div.appendChild(buttonDelete)
                    }
 
                    i++;

                    // Add the completed div to fragment
                    fragment.appendChild(div);
                    // Add the fragment to the main HTML's div where the data will be shown
                    document.getElementById("recipe_steps").appendChild(fragment);
                }
            }

            // After all data is shown create the segment for adding a new recipe
            let fragment = document.createDocumentFragment();

            // Create the button that will show the "form" for adding a new step
            let buttonAdd = document.createElement("button");
            buttonAdd.id = "add_button";
            buttonAdd.innerHTML = "Add a new step";
            buttonAdd.onclick = function() {
                // Calculate the next step's ID
                let next_step_id = Object.keys(recipe_data_object.data).length;

                let last_step_id = next_step_id - 1;

                let last_step_picture_path = recipe_data_object.data["step" + last_step_id]["step" + last_step_id + "_picture"];
                let next_step_picture = last_step_picture_path.substring(0, last_step_picture_path.lastIndexOf("/") + 1) + "step" + next_step_id + ".jpg";

                let temp = {};
                temp["step" + next_step_id + "_text"] = "Enter the step's text";
                temp["step" + next_step_id + "_picture"] = next_step_picture;
                recipe_data_object.data["step" + next_step_id] = temp;
                
                changeRecipeData(recipe_data_object);
            }
            fragment.appendChild(buttonAdd);
            document.getElementById("recipe_steps").appendChild(fragment);
        }
        if (this.readyState == 4 && this.status != 200) {       // not OK
            alert(url2);
        }
    };

    // GET THE RECIPE THUMBNAIL
    xmlhttp3.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)    // OK
        {
            try{
				var response = this.responseText;
			}
			catch(e){
				return;
			}

            let recipe_thumbnail = document.getElementById("thumbnail");
            recipe_thumbnail.src = "http://localhost/AutoRecipe/" + response;

            // Create a span element for the form parts that appear when the button for changing the picture is clicked
            let spanChangePicture = document.createElement("span");

            //Change recipe thumbnail
            document.getElementById("change_thumbnail").onclick = function() {
                // Remove all other input elements on the page
                Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
                    function(item) {
                        item.remove();
                    });
                if (document.contains(document.getElementById("input_button"))) {
                    document.getElementById("input_button").remove();
                }

                if (document.contains(document.getElementById("uploadform"))) {
                    document.getElementById("uploadform").remove();
                }

                // Create the form
                let change_picture_form = document.createElement("form");
                change_picture_form.id="uploadform";
                change_picture_form.style = "display:inline";
                
                // Create the input button for chosing the file
                let inputFile = document.createElement("input");
                inputFile.type = "file";
                inputFile.name = "file";

                // Create the input button for submission of the file
                let inputSubmit = document.createElement("input");
                inputSubmit.type = "submit";
                inputSubmit.value = "Upload Picture";

                // Add all new elements to the change picture form
                change_picture_form.appendChild(inputFile);
                change_picture_form.appendChild(inputSubmit);
                spanChangePicture.appendChild(change_picture_form);
                document.getElementById("thumbnail_div").appendChild(spanChangePicture);

                changeStepPicture("/AutoRecipe/recipe_pictures?picture_path=" + response);
            }
        }
        if(this.readyState == 4 && this.status != 200)     // not OK
        {
            console.log(this.status);
        }
    };

    xmlhttp1.open("GET", url1, true);     // Set method, URL of the request
    xmlhttp1.setRequestHeader("Accept", "application/json");
    xmlhttp1.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
    xmlhttp1.send();

    xmlhttp2.open("GET", url2, true);     // Set method, URL of the request
    xmlhttp2.setRequestHeader("Accept", "application/json");
    xmlhttp2.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
    xmlhttp2.send();
    
    xmlhttp3.open("GET", url3, true);     // Set method, URL of the request
    xmlhttp3.setRequestHeader("Accept", "application/json");
    xmlhttp3.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
    xmlhttp3.send();

    // Change recipe name
    document.getElementById("rename_recipe").onclick = function() {
        // Remove all other input elements on the page
        Array.prototype.slice.call(document.getElementsByTagName('input')).forEach(
            function(item) {
                item.remove();
            });
        if (document.contains(document.getElementById("input_button"))) {
            document.getElementById("input_button").remove();
        }

        if (document.contains(document.getElementById("uploadform"))) {
            document.getElementById("uploadform").remove();
        }

        // Create the input element where the new recipe name's text will be entered
        let input = document.createElement("input");
        input.id = "name_text";
        input.type = "text";
        input.required;

        // Create the submit button that'll save the new recipe name's text
        let input_button = document.createElement("button");
        input_button.id = "input_button";
        input_button.innerHTML = "Submit";
        input_button.onclick = function () {
            recipe_data_object.new_recipe_name = document.getElementById(input.id).value;
            setCookie("clicked_recipe_name", document.getElementById(input.id).value, 1)
            changeRecipeData(recipe_data_object);
        }
        // Append the two new elements to the step's div
        document.getElementById("rename_recipe_div").appendChild(input);
        document.getElementById("rename_recipe_div").appendChild(input_button);
    }
}