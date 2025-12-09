const eventsContainer = document.getElementById('eventsContainer');
const redeemForm = document.getElementById('redeemForm');
const redeemResult = document.getElementById('redeemResult');
let updateTimer;

function createCard(event) {
    const col = document.createElement('div');
    col.className = 'col-md-3 mb-4';

    const card = document.createElement('div');
    card.className = 'card h-100 shadow-sm';
    if (event.status === 'full') {
        card.classList.add('full-card');
    }

    const img = document.createElement('img');
    img.src = event.image_url || 'https://via.placeholder.com/400x250?text=Event';
    img.className = 'card-img-top';
    img.alt = event.name;

    const body = document.createElement('div');
    body.className = 'card-body text-center d-flex flex-column';

    const title = document.createElement('h5');
    title.className = 'card-title';
    title.textContent = event.name;

    const desc = document.createElement('p');
    desc.className = 'text-muted small';
    desc.textContent = event.description || 'Limited-time premium event';

    const seats = document.createElement('p');
    seats.className = 'card-text fw-semibold';
    seats.textContent = `Seats: ${event.used_seats}/${event.total_seats}`;

    const status = document.createElement('span');
    status.className = 'badge rounded-pill mb-3 event-status';
    status.textContent = event.status === 'full' ? 'Full' : 'Open';
    status.classList.add(event.status === 'full' ? 'bg-secondary' : 'bg-success');

    const button = document.createElement('button');
    button.className = 'btn btn-primary mt-auto';
    button.textContent = 'Redeem Key';
    button.dataset.bsToggle = 'modal';
    button.dataset.bsTarget = '#redeemModal';
    button.disabled = event.status === 'full';

    body.append(title, desc, seats, status, button);
    card.append(img, body);
    col.appendChild(card);
    return col;
}

async function updateEvents(showLoader = false) {
    if (showLoader) {
        eventsContainer.innerHTML = '<div class="loader"></div>';
    }

    try {
        const response = await fetch('fetch_events.php', { cache: 'no-store' });
        const data = await response.json();

        if (!data.success) {
            eventsContainer.innerHTML = `<div class="alert alert-danger">${data.message || 'Unable to load events.'}</div>`;
            return;
        }

        eventsContainer.innerHTML = '';
        if (!data.events || data.events.length === 0) {
            eventsContainer.innerHTML = '<p class="text-muted">No events available right now.</p>';
            return;
        }

        data.events.forEach((event) => {
            eventsContainer.appendChild(createCard(event));
        });
    } catch (error) {
        eventsContainer.innerHTML = `<div class="alert alert-danger">Failed to fetch events.</div>`;
    }
}

async function redeemKey(event) {
    event.preventDefault();
    redeemResult.textContent = '';

    const formData = new FormData(redeemForm);

    try {
        const response = await fetch('redeem.php', {
            method: 'POST',
            body: formData,
        });
        const data = await response.json();

        if (data.success) {
            redeemResult.innerHTML = `<div class="alert alert-success mb-0">${data.message}</div>`;
            redeemForm.reset();
            updateEvents();
        } else {
            redeemResult.innerHTML = `<div class="alert alert-warning mb-0">${data.message || 'Invalid or already used key.'}</div>`;
        }
    } catch (error) {
        redeemResult.innerHTML = '<div class="alert alert-danger mb-0">Unexpected error. Please try again.</div>';
    }
}

if (redeemForm) {
    redeemForm.addEventListener('submit', redeemKey);
}

updateEvents(true);
updateTimer = setInterval(updateEvents, 15000);
