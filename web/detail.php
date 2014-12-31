{% extends '::base.html.twig' %}
{% block title %}Bdthèque | Détails de la BD{% endblock %}

{% block body %}
<form class="affichageBddetail">
        <div class="bd1">
            <img class="imagedetail"src="{{ asset('img/115618_c.jpg') }}"title="bd one">
        </div>
        <div class="detailBddetail">
            <h3>titre de la bd: </h3>
            <div class="auteur">Auteur:
            	<p>Nom: </p>
            	<p>Prénom:</p>
            	<p>Aka: </p>
            	<p>BirthDate</p>
            	<p>DeathDate</p>
            	<p>Country</p>
            </div>
            <div class="coloriste">Coloriste: <br>
            	<p>Nom: </p>
            	<p>Prénom:</p>
            	<p>Aka: </p>
            	<p>BirthDate</p>
            	<p>DeathDate</p>
            	<p>Country</p>
            </div>
            <div class="scenariste">Scénariste: <br>
            	<p>Nom: </p>
            	<p>Prénom:</p>
            	<p>Aka: </p>
            	<p>BirthDate</p>
            	<p>DeathDate</p>
            	<p>Country</p>
            </div>
           
            <div class="serie">Série: <br>
            	<p>Style:</p>
            	<p>Comment: </p>
            	<p>Board</p>
            	<p>Language</p>
            </div>
            <div class="Bd">Bd: <br>
            	<p>Title: </p>
            	<p>publieur:</p>
            	<p>Isbn: </p>
            	<p>Pages:</p>
            </div>
        </div>
       	<div class="stockdetail">3 dispo</div>
        <input type="submit" class="ajouterdetail" value="Ajouter au panier !">
 </form>

{% endblock %}