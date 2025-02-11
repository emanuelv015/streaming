<?php require_once __DIR__ . '/../inc/config.php'; ?>

<style>
    .footer {
        background: rgba(0,0,0,0.3);
        padding: 60px 0 20px;
        margin-top: 60px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-section h3 {
        color: #fff;
        margin-bottom: 20px;
        font-size: 1.2em;
        font-weight: 600;
    }

    .footer-section p {
        color: rgba(255,255,255,0.7);
        line-height: 1.6;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin-bottom: 10px;
    }

    .footer-section ul li a {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-section ul li a:hover {
        color: var(--primary-color);
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: var(--primary-color);
        transform: translateY(-3px);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .footer-bottom p {
        color: rgba(255,255,255,0.5);
        font-size: 0.9em;
    }

    @media (max-width: 768px) {
        .footer {
            padding: 40px 0 20px;
        }

        .footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .footer-section {
            text-align: center;
        }

        .social-links {
            justify-content: center;
        }
    }
</style>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About <?php echo $site_title; ?></h3>
                <p>GETSPORTNEWS.UK don't host or stream any videos on our servers. All videos found on our site are found freely available around the web. Please address all DMCA Complaints where the videos are hosted or streamed.</p>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="<?php echo $base_url; ?>">Home</a></li>
                </ul>
            </div>
        </div>


    

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_title; ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

</div>
</body>
</html>