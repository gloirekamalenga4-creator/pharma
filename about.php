<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'À propos';

include 'includes/header.php';
?>

<div class="container">
    <div class="about-page">
        <div class="about-header">
            <h1>À propos de <?= SITE_NAME ?></h1>
            <p>Votre pharmacie en ligne de confiance</p>
        </div>

        <div class="about-content">
            <div class="about-section">
                <h2><i class="fas fa-hospital-user"></i> Notre mission</h2>
                <p>
                    <?= SITE_NAME ?> a pour mission de rendre les soins de santé accessibles à tous. 
                    Nous proposons une large gamme de médicaments, produits de parapharmacie et 
                    matériel médical de qualité, livrés directement à votre domicile.
                </p>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-bullseye"></i> Nos valeurs</h2>
                <div class="values-grid">
                    <div class="value-item">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Qualité</h4>
                        <p>Des produits certifiés et traçables</p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-heart"></i>
                        <h4>Bien-être</h4>
                        <p>Votre santé est notre priorité</p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h4>Confiance</h4>
                        <p>Une équipe à votre écoute</p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-rocket"></i>
                        <h4>Innovation</h4>
                        <p>Des solutions modernes pour votre santé</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-users"></i> Notre équipe</h2>
                <p>
                    Notre équipe est composée de pharmaciens diplômés et de professionnels de santé 
                    passionnés par leur métier. Nous sommes là pour vous conseiller et vous accompagner 
                    dans vos besoins de santé.
                </p>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-envelope"></i> Contactez-nous</h2>
                <p>
                    Une question, un conseil, une réclamation ? N'hésitez pas à nous contacter.
                </p>
                <div class="contact-info">
                    <p><i class="fas fa-phone"></i> <?= SITE_PHONE ?></p>
                    <p><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?= SITE_ADDRESS ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-page {
    padding: 40px 0;
}

.about-header {
    text-align: center;
    margin-bottom: 50px;
}

.about-header h1 {
    font-size: 32px;
    color: var(--dark);
}

.about-header p {
    color: var(--gray);
    font-size: 18px;
}

.about-section {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.about-section h2 {
    font-size: 22px;
    color: var(--dark);
    margin-bottom: 20px;
}

.about-section h2 i {
    color: var(--primary);
    margin-right: 10px;
}

.about-section p {
    color: var(--gray);
    line-height: 1.8;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.value-item {
    text-align: center;
    padding: 20px;
    background: var(--light);
    border-radius: 8px;
}

.value-item i {
    font-size: 40px;
    color: var(--primary);
    margin-bottom: 10px;
}

.value-item h4 {
    color: var(--dark);
    margin-bottom: 5px;
}

.value-item p {
    font-size: 14px;
    color: var(--gray);
}

.contact-info {
    margin-top: 15px;
}

.contact-info p {
    margin-bottom: 8px;
}

.contact-info i {
    width: 25px;
    color: var(--primary);
}
</style>

<?php include 'includes/footer.php'; ?>