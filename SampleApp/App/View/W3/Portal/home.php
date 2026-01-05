<style>
    .responsive-img {
        width: 100%;
        height: auto;
    }

    /* Mobile Portrait */
    @media (max-width: 767px) and (orientation: portrait) {
        .responsive-img {
            content: url("<?= BASE_URL ?>images/concert-big-picture-mobile2.webp");
        }
    }
</style>
<div style="background-color:#ede9e8;">
    <div style="max-width:1000px;margin:auto;">
        <img class="responsive-img" src="<?= BASE_URL ?>images/concert-big-picture.webp" alt="Home">
    </div>
</div>

<div class="contentbox" style="background-color:var(--rd-nav-light);max-width:1000px;margin:auto;">
    <p>
        Arkafe CONCERT is a management tool for architecture, interior, and other design consultancy studios. It helps build a standardized and efficient studio workflow for project delivery by providing a platform to plan, monitor, and effectively manage time and cost.
    </p>
    <p>
        CONCERT is an extensible framework providing a medium for custom applications as per the studio's requirements. Custom Business Apps can be built on an individual need basis.
    </p>
    <p>
        Write to <?= MAILTO ?> for a demo. A free trial is also available on request.
    </p>
</div>

<div class="contentbox" style="text-align: center;">
    <a class="button-18" href="<?= BASE_URL ?>trial">Free Trial</a>
    <a class="button-18" href="<?= BASE_URL ?>pricing">Pricing</a>
    <a class="button-18 button-18offer" href="<?= BASE_URL ?>offers">2026 Offers</a>
</div>