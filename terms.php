<?php
include 'inc/conf.php';
include 'layouts/header.php';
?>

<div class="container terms-container">
    <h1>Terms of Service</h1>
    
    <div class="terms-section">
        <h2>1. Terms</h2>
        <p>By accessing this website, you are agreeing to be bound by these Terms of Service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws.</p>
        
        <h2>2. Disclaimer</h2>
        <p>This page outlines the terms and conditions which you must agree to when using this website. By visiting any section of the site, you agree to accept and be bound by these Terms of Service (TOS).</p>
        
        <h2>3. Content</h2>
        <p>All content found on this site is found freely available around the web. We do not host or stream any videos on our servers. All videos, streams, or other content found on this website is hosted on third-party websites that are not owned or controlled by us.</p>
        
        <h2>4. Copyright</h2>
        <p>If you believe that your copyrighted work has been copied in a way that constitutes copyright infringement and is accessible on this site, you may notify our copyright agent. Please direct all inquiries to the appropriate legal teams.</p>
        
        <h2>5. DMCA</h2>
        <p>Please address all DMCA Complaints to the appropriate hosting services where the videos are hosted or streamed. We do not host any copyrighted material.</p>
        
        <h2>6. Use License</h2>
        <p>Permission is granted to temporarily access the materials (information or software) on our website for personal, non-commercial transitory viewing only.</p>
        
        <h2>7. Links</h2>
        <p>We have not reviewed all of the sites linked to this Website and we are not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by us of the site.</p>
    </div>
</div>

<style>
.terms-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 12px;
    color: #fff;
}

.terms-container h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #ff5529;
    font-size: 2em;
}

.terms-section {
    background: rgba(0, 0, 0, 0.2);
    padding: 30px;
    border-radius: 8px;
}

.terms-section h2 {
    color: #ff5529;
    margin: 25px 0 15px;
    font-size: 1.4em;
}

.terms-section p {
    color: #ccc;
    line-height: 1.6;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .terms-container {
        margin: 20px auto;
        padding: 15px;
    }
    
    .terms-section {
        padding: 20px;
    }
}
</style>

<?php
include 'layouts/footer.php';
?>
