<style>
    .offers {
        max-width: 550px;
        text-align: center;
        margin: auto;
    }

    .offers h1 {
        font-family: "Roboto Bold";
        background: #FFB703;
        color: #0F355A;
        padding: 14px 32px;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .offers p,
    .offers ul li {
        text-align: left;
        line-height: 1.5rem;
    }

    .offer-card {
        background: #1B4F7A;
        padding: 24px 32px;
        border-radius: 10px;
        max-width: 720px;
        margin: 24px auto;
    }

    ul {
        padding-left: 24px;
    }
</style>

<div class="contentbox" style="text-align: center;">
    <a class="button-18" href="<?= BASE_URL ?>home">Home</a>
    <a class="button-18" href="<?= BASE_URL ?>trial">Free Trial</a>
    <a class="button-18" href="<?= BASE_URL ?>pricing">Pricing</a>
    <!-- <a class="button-18 button-18offer" href="<?= BASE_URL ?>offers">2026 Offers</a> -->
</div>

<div class="offers">
    <h1>2026 Offers</h1>

    <div class="offer-card">

        <p>One time offer for subscribing Arkafe in January 2026. Pick any one.</p>

        <ul>
            <li>Annual subscription - 15% discount or get 3 months extra</li>
            <li>Quaterly subscription - get 1 month extra</li>
            <li>Under 10 users - Express edition at Rs. 60k for entire year</li>
        </ul>

        <img
            src="<?= BASE_URL ?>images/arkafe-2026.png"
            alt="New Year Offers"
            style="width:90%;max-width:350px;max-height:350px;">

    </div>
</div>