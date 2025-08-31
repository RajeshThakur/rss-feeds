let offset = 0;
let loading = false;
const limit = 9;

async function loadMoreArticles() {
    if (loading) return;
    loading = true;

    const res = await fetch(`rss_loader.php?offset=${offset}`);
    const data = await res.json();

    if (data.length === 0) return;

    const container = document.getElementById('article-container');

    data.forEach(article => {
        const card = document.createElement('div');
        card.className = 'card';

        if (article.image) {
            const img = document.createElement('img');
            img.src = article.image;
            card.appendChild(img);
        }

        const content = document.createElement('div');
        content.className = 'card-content';

        content.innerHTML = `
            <h3><a href="${article.link}" target="_blank">${article.title}</a></h3>
            <div class='date'>${article.pubDate} | ${article.source}</div>
        `;
        card.appendChild(content);
        container.appendChild(card);
    });

    offset += limit;
    loading = false;
}

window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 300) {
        loadMoreArticles();
    }
});

window.addEventListener('DOMContentLoaded', loadMoreArticles);
