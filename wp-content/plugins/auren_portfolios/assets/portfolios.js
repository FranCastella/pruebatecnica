
document.getElementById("portfolios_ALL").addEventListener("click", function() {
    var elems = document.getElementsByClassName('portfolios_cat');
    for (var i=0;i<elems.length;i+=1){
        elems[i].parentElement.classList.remove('hidden');
        }
    var buttons = this.parentNode.childNodes;
    console.log(buttons);
    for (var i=1;i<buttons.length;i+=1){
        buttons[i].classList.add('boton_filtrado');
        }

  });

  var botones = document.getElementsByClassName("portfolios_filtro_boton");
  var clase = [];
  if(botones){
  for(var i=0;i<botones.length;i++){
    clase[i] = 'portfolio_'+botones[i]["innerHTML"];
    botones[i].addEventListener("click", function() {
        var mostrar = false;
        if(this.classList.contains("boton_filtrado")){
            mostrar=true;
        }
        this.classList.toggle("boton_filtrado");
        var elems = document.getElementsByClassName('portfolio_'+this.innerHTML);
        for (var i=0;i<elems.length;i+=1){
            if(mostrar==true){
                elems[i].parentElement.parentElement.classList.add('hidden');
            }else{
                elems[i].parentElement.parentElement.classList.remove('hidden');
            }
            
            }
      });
  }
}
