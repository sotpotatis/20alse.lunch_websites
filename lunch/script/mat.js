    let menu_items = $("#items")
    let error_box = $("#error-box")
    let error_box_template_content = error_box.html()
    let error_box_template = Handlebars.compile(error_box_template_content)
    error_box.hide() // Göm felbox.
    let menu_items_template_content = menu_items.html()
    let menu_items_template = Handlebars.compile(menu_items_template_content)
    menu_items.html(menu_items_template({loading: true}))  // Rendera tom text
    Handlebars.registerHelper('ifEquals', function(arg1, arg2, options) {
    return (arg1 == arg2) ? options.fn(this) : options.inverse(this);
    });
    console.log("Hämtar data från API...")
    $.getJSON("https://lunchmeny.albins.website/api/", function lunch_retrieved(data){
        console.log("Lunchdata hämtad!")
        if (data.status === "success"){
            console.log("Lyckad hämtning. Renderar innehåll...")
	    // Hämta nuvarande dag
            var today = luxon.DateTime.now().setZone("Europe/Stockholm")
	    // Hämta nuvarande dag
            var today = luxon.DateTime.now().setZone("Europe/Stockholm")
            var weekday = today.weekday;
	    //data.menu.days[weekday-1]["is_today"] = true
            menu_items.html(menu_items_template({loading: false, menu_days: data.menu.days}))
        }
        else {
            console.log("Misslyckad hämtning. Renderar fel...")
            error_box.show() // Visa felruta
            error_box.html(error_box_template({error_message: data.message}))
        }
    }).fail(function (jqXHR, textStatus, errorThrown){
        console.log("Misslyckad förfrågan. Renderar fel...")
        error_box.show() // Visa felruta
	let error_message = "Fel: " + jqXHR.status
	if (jqXHR.responseJSON !== null){
		error_message = "Fel: " + jqXHR.responseJSON.message + " (" + jqXHR.status + ")"
	}
        error_box.html(error_box_template({error_message: error_message}))
    })