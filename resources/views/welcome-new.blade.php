<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Bulk Messenger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #25D366;
            --secondary-color: #34B7F1;
            --dark-color: #075E54;
            --warning-color: #FFC107;
            --info-color: #2196F3;
            --gradient-primary: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%);
        }
        
        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .feature-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 16px;
            padding: 1.5rem;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .step-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 20px;
            overflow: hidden;
            height: 100%;
        }
        
        .step-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/" style="color: var(--primary-color);">
                <i class="fab fa-whatsapp me-2"></i>WhatsApp Bulk Messenger
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('filament.admin.auth.login') }}">Login</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center mt-4 mb-5">
            <div class="col-12">
                <div class="hero-section text-white position-relative" style="padding: 4rem 3rem;">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h1 class="display-3 mb-4 fw-bold" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                                <i class="fab fa-whatsapp me-3" style="animation: pulse 2s infinite;"></i>
                                WhatsApp Bulk Messenger
                            </h1>
                            <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                Send WhatsApp messages to multiple contacts efficiently and cost-effectively. 
                                Manage your contacts, create templates, and schedule messages with ease.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-light btn-lg me-3" style="font-weight: 600; padding: 0.75rem 2rem;">
                                    <i class="fas fa-sign-in-alt me-2"></i> Access Admin Panel
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center mt-4 mt-lg-0">
                            <div style="font-size: 8rem; opacity: 0.2;">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" style="color: var(--dark-color);">Powerful Features</h2>
                <p class="lead text-muted">Everything you need to manage your WhatsApp messaging campaigns</p>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card feature-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, rgba(37, 211, 102, 0.2) 0%, rgba(18, 140, 126, 0.2) 100%); color: var(--primary-color);">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Single & Bulk Messages</h5>
                        <p class="text-muted mb-0">Send messages to individuals or groups with ease and efficiency.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card feature-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, rgba(52, 183, 241, 0.2) 0%, rgba(33, 150, 243, 0.2) 100%); color: var(--secondary-color);">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Contact Management</h5>
                        <p class="text-muted mb-0">Organize contacts and create groups for targeted messaging.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card feature-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 152, 0, 0.2) 100%); color: var(--warning-color);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Message Templates</h5>
                        <p class="text-muted mb-0">Create and reuse message templates with dynamic variables.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card feature-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.2) 0%, rgba(25, 118, 210, 0.2) 100%); color: var(--info-color);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Message Scheduling</h5>
                        <p class="text-muted mb-0">Schedule messages to be sent at optimal times automatically.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" style="color: var(--dark-color);">How It Works</h2>
                <p class="lead text-muted">Simple steps to start sending bulk WhatsApp messages</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card step-card border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="step-number">1</div>
                        <div class="mb-4" style="font-size: 3rem; color: var(--primary-color);">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Import Contacts</h4>
                        <p class="text-muted mb-0">Upload your contacts via CSV or add them individually to the system. Organize them into groups for better targeting.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card step-card border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="step-number">2</div>
                        <div class="mb-4" style="font-size: 3rem; color: var(--secondary-color);">
                            <i class="fas fa-pen-fancy"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Create Message</h4>
                        <p class="text-muted mb-0">Compose your message or use a pre-defined template with dynamic variables for personalized messaging.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card step-card border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="step-number">3</div>
                        <div class="mb-4" style="font-size: 3rem; color: var(--dark-color);">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Send or Schedule</h4>
                        <p class="text-muted mb-0">Send immediately or schedule for later delivery with cost optimization settings and batch processing.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(37, 211, 102, 0.05) 0%, rgba(18, 140, 126, 0.05) 100%);">
                    <div class="card-body p-5 text-center">
                        <h3 class="fw-bold mb-4" style="color: var(--dark-color);">Ready to Get Started?</h3>
                        <p class="lead text-muted mb-4">Start sending bulk WhatsApp messages today with our easy-to-use platform.</p>
                        <div>
                            <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Access Admin Panel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} WhatsApp Bulk Messenger. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
