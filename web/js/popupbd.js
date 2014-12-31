

popup = {

    /*
     * 3.1. Création de la popup
     */
    init: function() {
        console.log("popup init")

        // Je crée une popup
        this.overlay = $("<div>", {
            css: {
                position: "fixed",
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
                backgroundColor: "rgba(0,0,0,0.6)",
                padding: 50,
                display: "none",
                zIndex: 999
            }
        })

        // J'ajoute au DOM
        $("body").append( this.overlay )

        // On écoute le clic sur #back. On écoute sur document, car #back n'existe pas encore.
        $(document).on("click", "#back", this.ferme)

    },

    /*
     * 3.2. Ajouter un contenu
     */
    affiche: function(x) {
        console.log("popup affiche")

        // J'ajoute le contenu, puis j'affiche
        this.overlay.append(x).fadeIn()
        //diese()


    },

    /*
     * 3.3. Fermer la popup
     */
    ferme: function() {
        console.log("popup ferme")

        // Je fais disparaitre, puis j'efface le contenu
        popup.overlay.fadeOut({
            complete: function() {
                console.log("overlay ferme > fadeOut complete")
                $(this).empty()
            }
        })

        //je remats mon hash à zero
        diese.changeHash("")

        // Prevent Default
        return false

    },
    /*
     * Ajoute un bouton back
     */
    addBack: function() {
        this.overlay.append($('<a id="back">Back</a>'))
    },


}


app = {

    /*
     * Chargement
     */
    init: function() {
        console.log("app init")

        /* 3.1. Charger la popup */
        popup.init()
       
       

        // 1. Clic sur une bd
        $(".plusdetail").on("submit", this.mabd)


    },
   



/*
     *  2. Clic sur un poster
     */
    mabd: function() {
        console.log("mon image")

        // 2.1. Mon poster
        var poster = this

        // Je vais chercher les détails
        $.ajax({
            url: poster.href,
            success: function(html) {
                console.log("mon image > ajax success")
                
                // 2.2. Détails
                console.log($(html))
                var details = $(html).find(".affichageBddetail")


                // 3.2. Aficher les détails
                popup.affiche( details )

               
            }
        })


        // Prevent Default
        return false
    }

}

/*
 * Chargement du DOM
 */
$(function() {
    app.init()
})