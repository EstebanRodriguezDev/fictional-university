class MyNotes {
 constructor() {
  this.notaEliminada = document.querySelectorAll(".delete-note");
  this.events();
 }

 events() {
  this.notaEliminada.forEach(nota => {
   nota.addEventListener("click", this.deleteNote);
  });
 }

 // Metodos aqui
 deleteNote() {
  console.log('Nota eliminada')
 }
}

export default MyNotes;