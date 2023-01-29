function changeRecipeData(recipe_data_object)
{
    let url = "/AutoRecipe/recipes?recipe_ID=" + getCookie("clicked_recipe_ID");
    let JSONdata = JSON.stringify(recipe_data_object, null, "  ");	// convert object into a JSON string
    
    let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
    xmlhttp.open("PUT", url, true);   // Set method, URL of the request
    xmlhttp.setRequestHeader("Accept", "application/json");
    xmlhttp.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
     
    xmlhttp.onreadystatechange = function()    // Code to run depending on the response
    {
        if (this.readyState == 4 && this.status == 200)    // OK
        {
            // Clear all the section divs on the html document
            let ingredients = document.getElementById("recipe_ingredients");
            ingredients.innerHTML = "";
            let description = document.getElementById("recipe_description");
            description.innerHTML = "";
            let steps = document.getElementById("recipe_steps");
            steps.innerHTML = "";
            // Reload the data
            getRecipeData();
        }
        if(this.readyState == 4 && this.status != 200)     // not OK
        {
            console.log(this.status);
        }
    };
    
    xmlhttp.send(JSONdata);
}

function deleteStepPicture(picture_path) {
    let url = "/AutoRecipe/recipe_pictures?picture_path=" + picture_path;

    let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
    xmlhttp.open("DELETE", url, true);   // Set method, URL of the request
    xmlhttp.setRequestHeader("Accept", "application/json");
    xmlhttp.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
     
    xmlhttp.onreadystatechange = function()    // Code to run depending on the response
    {
        if (this.readyState == 4 && this.status == 200)    // OK
        {
            console.log(this.status);
        }
        if(this.readyState == 4 && this.status != 200)     // not OK
        {
            console.log(this.status);
        }
    };
     
    xmlhttp.send();
}

function changeStepPicture (picture_url) {
    $("form#uploadform").submit(function () {
        var formData = new FormData(this);

        $.ajax({
            url: picture_url,
            type: 'POST',
            data: formData,
            headers: {
                "Authorization": "Bearer " + getCookie("token")
              },
            async: false,
            success: function() {
                window.location.reload(true);
            },
            error:function(data){
                window.location.reload(true);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });
}

function AddStepPicture (picture_url) {
    $("form#uploadform").submit(function () {
        var formData = new FormData(this);

        $.ajax({
            url: picture_url,
            type: 'POST',
            data: formData,
            headers: {
                "Authorization": "Bearer " + getCookie("token")
              },
            async: false,
            success: function(data) {
                console.log(data);
            },
            error:function(data){
                console.log(data);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });
}

function deleteRecipe(recipe_ID) {
    let url = "/AutoRecipe/recipes?recipe_ID=" + recipe_ID;

    let xmlhttp = new XMLHttpRequest();	// create the (empty) HTTP request
    xmlhttp.open("DELETE", url, true);   // Set method, URL of the request
    xmlhttp.setRequestHeader("Accept", "application/json");
    xmlhttp.setRequestHeader("Authorization", "Bearer " + getCookie("token"));
     
    xmlhttp.onreadystatechange = function()    // Code to run depending on the response
    {
        if (this.readyState == 4 && this.status == 200)    // OK
        {
            // Remove the input field and input button if they exist
            var input_field = document.getElementById("new_name");
            if(input_field != null) {
                input_field.remove();
            }
            let input_button = document.getElementById("input_button");
            if(input_button != null) {
                input_button.remove();
            }
            getRecipeList();
        }
        if(this.readyState == 4 && this.status != 200)     // not OK
        {
            console.log(this.status);
        }
    };
     
    xmlhttp.send();
}