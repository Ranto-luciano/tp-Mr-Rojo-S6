// Scripts pour l'interface d'administration

// Confirmation avant suppression
document.addEventListener('DOMContentLoaded', function() {
    // Génération automatique du slug à partir du titre
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('blur', function() {
            if (!slugInput.value.trim()) {
                slugInput.value = generateSlug(titleInput.value);
            }
        });
    }
    
    // Mise à jour en temps réel du compteur de caractères pour meta_description
    const metaDesc = document.getElementById('meta_description');
    if (metaDesc) {
        const counter = document.createElement('small');
        counter.className = 'text-muted';
        counter.style.display = 'block';
        metaDesc.parentNode.appendChild(counter);
        
        function updateCounter() {
            const length = metaDesc.value.length;
            counter.innerHTML = `${length} caractères (150-160 recommandés)`;
            if (length > 160) {
                counter.style.color = 'red';
            } else if (length > 150) {
                counter.style.color = 'orange';
            } else {
                counter.style.color = '#6c757d';
            }
        }
        
        metaDesc.addEventListener('input', updateCounter);
        updateCounter();
    }
});

/**
 * Génère un slug à partir d'une chaîne
 */
function generateSlug(str) {
    str = str.toLowerCase();
    str = str.replace(/[àáâãä]/g, 'a');
    str = str.replace(/[èéêë]/g, 'e');
    str = str.replace(/[ìíîï]/g, 'i');
    str = str.replace(/[òóôõö]/g, 'o');
    str = str.replace(/[ùúûü]/g, 'u');
    str = str.replace(/[ýÿ]/g, 'y');
    str = str.replace(/[ç]/g, 'c');
    str = str.replace(/[^a-z0-9]+/g, '-');
    str = str.replace(/^-+|-+$/g, '');
    return str;
}