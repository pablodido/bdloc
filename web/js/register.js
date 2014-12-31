

/************************
 *   Objet principal    *
 ************************/

register = {
    
    
    /* Chargement du DOM */
    init: function() {
        console.log("register.init")
           // Autocompletion d'address
        this.adresses()

    },

    adresses: function() {
        console.log("register.adresses")

        // On chope notre input
        var input = document.getElementById("bdloc_appbundle_register_address")
        //console.log(input)

        // Paramètres
        var options = {
            types: ['address'],
            componentRestrictions: {
                country: 'fr'
            }
        }

        // On ajoute l'autocomplete
        var autocomplete = new google.maps.places.Autocomplete(input, options)
       }
}




/*************************
 *  Chargement du DOM    *
 *  = chargement du html *
 *************************/

$(function() {
    console.log("chargement du dom")
    register.init()
})