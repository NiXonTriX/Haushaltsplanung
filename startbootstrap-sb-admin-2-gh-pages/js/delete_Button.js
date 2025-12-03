document.getElementById("confirmDeleteBooking").addEventListener("click", function () {
    // ➤ Hier deine Lösch-Aktion
    console.log("Buchung wurde gelöscht!");

    // Modal schließen
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBookingModal'));
    modal.hide();

    // Beispiel: Nachricht anzeigen
    alert("Buchung erfolgreich gelöscht ✔️");
});
