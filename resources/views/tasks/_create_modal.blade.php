<div class="modal fade" id="modalCreateTask" tabindex="-1" aria-labelledby="modalCreateTaskLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf
                <input type="hidden" name="auto_score" value="1">

                <div class="modal-header">
                    <h4 class="modal-title" id="modalCreateTaskLabel">Nueva Tarea</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mt_title" class="form-label">Título</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="mt_title" name="title" value="{{ old('title') }}"
                               placeholder="¿Qué hay que hacer?" required autofocus>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="mt_description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="mt_description" name="description" rows="3"
                                  placeholder="Detalles adicionales (opcional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="mt_due_date" class="form-label">Fecha límite</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                               id="mt_due_date" name="due_date"
                               value="{{ old('due_date', now()->toDateString()) }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="small mt-2" aria-live="polite">
                        <span id="mt_badge_diff" class="badge bg-secondary me-2">Dificultad: —</span>
                        <span id="mt_badge_pts" class="badge bg-info text-dark">Puntos: —</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const titleInput = document.getElementById('mt_title');
    const descInput = document.getElementById('mt_description');
    const dateInput = document.getElementById('mt_due_date');
    const badgeDiff = document.getElementById('mt_badge_diff');
    const badgePts = document.getElementById('mt_badge_pts');

    const highKeywords = ['deploy', 'migración', 'docker', 'integrar', 'autenticación', 'api', 'seguridad', 'optimizar', 'refactor', 'producción', 'kubernetes', 'azure', 'firebase', 'nginx', 'bug', 'incidente'];
    const mediumKeywords = ['investigar', 'configurar', 'modelo', 'consulta', 'diagrama', 'endpoint', 'testing'];
    const lowKeywords = ['reunión', 'leer', 'documentar', 'nota'];

    function calculatePreview() {
        const title = titleInput.value || '';
        const description = descInput.value || '';
        const dueDate = dateInput.value;

        if (!title && !description) {
            badgeDiff.textContent = 'Dificultad: —';
            badgeDiff.className = 'badge bg-secondary me-2';
            badgePts.textContent = 'Puntos: —';
            return;
        }

        let score = 0;
        let urgencyMultiplier = 1.00;

        // Urgency
        if (dueDate) {
            const now = new Date();
            now.setHours(0, 0, 0, 0);
            const due = new Date(dueDate);
            due.setHours(0, 0, 0, 0);
            const diffTime = due - now;
            const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (days <= 0) {
                score += 40;
                urgencyMultiplier = 1.25;
            } else if (days <= 2) {
                score += 30;
                urgencyMultiplier = 1.15;
            } else if (days <= 5) {
                score += 15;
                urgencyMultiplier = 1.08;
            }
        }

        // Length
        const words = description.split(/\s+/).filter(w => w.length > 0).length;
        if (words > 100) score += 35;
        else if (words > 50) score += 20;
        else if (words > 20) score += 10;

        // Keywords
        const textLower = (description + ' ' + title).toLowerCase();

        let highCount = 0;
        highKeywords.forEach(kw => { if (textLower.includes(kw)) highCount++; });
        score += Math.min(30, highCount * 10);

        let mediumCount = 0;
        mediumKeywords.forEach(kw => { if (textLower.includes(kw)) mediumCount++; });
        score += Math.min(18, mediumCount * 6);

        let lowCount = 0;
        lowKeywords.forEach(kw => { if (textLower.includes(kw)) lowCount++; });
        let lowScore = Math.min(9, lowCount * 3);
        if (lowCount > 0 && highCount === 0 && mediumCount === 0) lowScore -= 3;
        score += lowScore;

        // Time estimate
        const hoursMatch = description.match(/(\d+)\s*(h|hr|hora|horas)/i);
        const minutesMatch = description.match(/(\d+)\s*(min|m)/i);
        if (hoursMatch) {
            const hours = parseInt(hoursMatch[1]);
            score += Math.min(20, 5 * hours);
        } else if (minutesMatch) {
            score += 5;
        }

        // Map to difficulty
        let difficulty = 1;
        if (score >= 60) difficulty = 3;
        else if (score >= 30) difficulty = 2;

        // Points
        const basePoints = difficulty === 1 ? 10 : (difficulty === 2 ? 20 : 40);
        let points = Math.floor(basePoints * urgencyMultiplier);
        const lengthBonus = Math.floor(words / 15) * 2;
        points += lengthBonus;
        points = Math.max(5, Math.min(100, points));

        // Update badges
        const diffText = ['', 'Fácil', 'Media', 'Difícil'][difficulty];
        const diffClass = ['', 'bg-success', 'bg-warning text-dark', 'bg-danger'][difficulty];
        badgeDiff.textContent = 'Dificultad: ' + diffText;
        badgeDiff.className = 'badge me-2 ' + diffClass;
        badgePts.textContent = 'Puntos: ' + points;
    }

    if (titleInput) {
        titleInput.addEventListener('input', calculatePreview);
        descInput.addEventListener('input', calculatePreview);
        dateInput.addEventListener('change', calculatePreview);
    }
})();
</script>

@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', () => {
    const m = new bootstrap.Modal(document.getElementById('modalCreateTask'));
    m.show();
});
</script>
@endif
