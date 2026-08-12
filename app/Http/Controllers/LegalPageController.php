<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function terms(): View
    {
        return view('pages.legal', [
            'type' => 'Terms of Service',
            'eyebrow' => 'Legal',
            'title' => 'Terms of Service',
            'intro' => 'The rules and conditions governing your use of the Senflux platform and website.',
            'updated' => 'August 12, 2026',
            'sections' => $this->termsSections(),
        ]);
    }

    public function privacy(): View
    {
        return view('pages.legal', [
            'type' => 'Privacy Policy',
            'eyebrow' => 'Privacy',
            'title' => 'Privacy Policy',
            'intro' => 'How Senflux collects, uses, protects, and manages information associated with our services.',
            'updated' => 'August 12, 2026',
            'sections' => $this->privacySections(),
        ]);
    }

    public function cookies(): View
    {
        return view('pages.legal', [
            'type' => 'Cookie Policy',
            'eyebrow' => 'Privacy',
            'title' => 'Cookie Policy',
            'intro' => 'How cookies and similar technologies are used across Senflux websites and services.',
            'updated' => 'August 12, 2026',
            'sections' => $this->cookieSections(),
        ]);
    }

    public function security(): View
    {
        return view('pages.legal', [
            'type' => 'Security',
            'eyebrow' => 'Trust',
            'title' => 'Security at Senflux',
            'intro' => 'Our approach to protecting systems, information, infrastructure, and the integrity of our platform.',
            'updated' => 'August 12, 2026',
            'sections' => $this->securitySections(),
        ]);
    }

    public function disclosures(): View
    {
        return view('pages.legal', [
            'type' => 'Disclosures',
            'eyebrow' => 'Important Information',
            'title' => 'Disclosures',
            'intro' => 'Important information regarding Senflux data, analytics, market information, and the use of our platform.',
            'updated' => 'August 12, 2026',
            'sections' => $this->disclosureSections(),
        ]);
    }


    private function termsSections(): array
    {
        return [
            [
                'title' => '1. Acceptance of Terms',
                'body' => [
                    'These Terms of Service govern your access to and use of Senflux websites, applications, dashboards, APIs, research, data products, and related services (collectively, the “Services”).',
                    'By accessing or using the Services, you agree to be bound by these Terms. If you do not agree with these Terms, you should not access or use the Services.',
                    'If you are using the Services on behalf of an organization, you represent that you have authority to bind that organization to these Terms.'
                ],
            ],
            [
                'title' => '2. Description of the Services',
                'body' => [
                    'Senflux provides analytics and intelligence derived from blockchain, market, wallet, liquidity, and ecosystem activity. The Services are designed to help users understand participation, market formation, capital behavior, and related signals.',
                    'The Services may include dashboards, scores, classifications, alerts, visualizations, research, datasets, APIs, reports, and other analytical outputs.',
                    'We may modify, improve, suspend, or discontinue portions of the Services from time to time. Where practical, we will provide reasonable notice of material changes.'
                ],
            ],
            [
                'title' => '3. Eligibility and Accounts',
                'body' => [
                    'You must provide accurate information when creating an account or interacting with the Services. You are responsible for maintaining the confidentiality of credentials associated with your account.',
                    'You are responsible for activity occurring through your account unless you have promptly notified us of unauthorized access.',
                    'You may not create an account or use the Services for unlawful purposes or in violation of these Terms.'
                ],
            ],
            [
                'title' => '4. Acceptable Use',
                'body' => [
                    'You agree not to misuse the Services, interfere with their operation, attempt to gain unauthorized access, circumvent security controls, reverse engineer proprietary systems except where expressly permitted by applicable law, or use the Services to violate the rights of others.',
                    'You must not use automated methods to excessively scrape, overload, probe, or otherwise interfere with Senflux infrastructure.',
                    'You must comply with all laws and regulations applicable to your use of the Services.'
                ],
            ],
            [
                'title' => '5. Data and Analytical Outputs',
                'body' => [
                    'Senflux analytics are generated from available data sources and proprietary analytical methods. Data may be incomplete, delayed, inaccurate, unavailable, or subject to changes in underlying networks and markets.',
                    'Scores, classifications, signals, rankings, alerts, and other outputs should be treated as analytical information rather than guarantees of future outcomes.',
                    'You are responsible for independently evaluating information before relying on it for business, investment, trading, or other decisions.'
                ],
            ],
            [
                'title' => '6. Intellectual Property',
                'body' => [
                    'Senflux and its licensors retain all rights, title, and interest in the Services, software, designs, trademarks, documentation, methodologies, and other proprietary materials.',
                    'Except as expressly permitted, you may not reproduce, redistribute, resell, publicly display, or commercially exploit proprietary Senflux materials.',
                    'Nothing in these Terms transfers ownership of Senflux intellectual property to you.'
                ],
            ],
            [
                'title' => '7. Third-Party Services',
                'body' => [
                    'The Services may reference or integrate third-party networks, protocols, exchanges, data providers, infrastructure providers, or other services.',
                    'Senflux does not control third-party services and is not responsible for their availability, accuracy, security, policies, or performance.',
                    'Your use of third-party services may be subject to separate terms and policies.'
                ],
            ],
            [
                'title' => '8. Fees and Subscriptions',
                'body' => [
                    'Certain Services may be offered on a paid or subscription basis. Applicable pricing, billing periods, usage limits, and cancellation conditions will be presented at the time of purchase or subscription.',
                    'Unless otherwise stated, fees are non-refundable to the extent permitted by applicable law.',
                    'We may change pricing for future billing periods with reasonable notice.'
                ],
            ],
            [
                'title' => '9. Disclaimers',
                'body' => [
                    'The Services are provided on an “as available” and “as is” basis to the maximum extent permitted by law.',
                    'Senflux does not warrant that the Services will be uninterrupted, error-free, complete, current, or suitable for every particular purpose.',
                    'Nothing provided through the Services constitutes investment, financial, legal, tax, accounting, or other professional advice.'
                ],
            ],
            [
                'title' => '10. Limitation of Liability',
                'body' => [
                    'To the maximum extent permitted by applicable law, Senflux and its affiliates, officers, employees, contractors, and licensors will not be liable for indirect, incidental, special, consequential, exemplary, or punitive damages arising from or related to your use of the Services.',
                    'Any additional limitations applicable to a specific paid service may be stated in a separate agreement.'
                ],
            ],
            [
                'title' => '11. Termination',
                'body' => [
                    'You may stop using the Services at any time. Senflux may suspend or terminate access where reasonably necessary to protect the Services, users, infrastructure, or comply with law.',
                    'Provisions that by their nature should survive termination will remain in effect.'
                ],
            ],
            [
                'title' => '12. Changes to These Terms',
                'body' => [
                    'We may update these Terms as our Services, business, or legal requirements evolve. Updated Terms will be published through the relevant Senflux website or application.',
                    'Your continued use of the Services after an updated version becomes effective constitutes acceptance of the revised Terms, to the extent permitted by law.'
                ],
            ],
        ];
    }


    private function privacySections(): array
    {
        return [
            [
                'title' => '1. Overview',
                'body' => [
                    'This Privacy Policy explains how Senflux may collect, use, disclose, retain, and protect information when you use our websites, applications, products, and services.',
                    'We aim to collect information responsibly and only use it for legitimate business, operational, security, and product purposes.'
                ],
            ],
            [
                'title' => '2. Information We Collect',
                'body' => [
                    'We may collect information you provide directly, including your name, email address, company, account details, communications, and information submitted through forms.',
                    'We may also collect technical information such as IP address, browser type, device information, operating system, approximate location derived from technical information, timestamps, and interaction data.',
                    'Where applicable, we collect information relating to your use of the Services, including account activity, preferences, and product interactions.'
                ],
            ],
            [
                'title' => '3. Blockchain and Public Data',
                'body' => [
                    'Senflux analyzes information originating from public blockchain networks and other publicly available or commercially licensed data sources.',
                    'Blockchain networks are generally public by design. Transactions and wallet addresses may be permanently recorded on a blockchain and may be visible to anyone with access to the relevant network.',
                    'Senflux does not control the underlying blockchain networks and cannot remove information that has already been recorded on a public ledger.'
                ],
            ],
            [
                'title' => '4. How We Use Information',
                'body' => [
                    'We may use information to provide, maintain, secure, and improve our Services; respond to enquiries; communicate with users; prevent fraud and abuse; monitor system performance; conduct analytics; and comply with legal obligations.',
                    'We may also use aggregated or de-identified information to understand trends, improve our products, and develop new analytical capabilities.'
                ],
            ],
            [
                'title' => '5. Communications',
                'body' => [
                    'If you contact us, we may retain the information contained in your communication so that we can respond and maintain appropriate business records.',
                    'We may send service-related communications that are necessary for operating your account or providing the Services. Marketing communications, where applicable, may include an unsubscribe mechanism.'
                ],
            ],
            [
                'title' => '6. Service Providers',
                'body' => [
                    'We may use trusted third-party providers for hosting, infrastructure, analytics, communications, payment processing, security, customer support, and other operational functions.',
                    'These providers may process information on our behalf and are expected to handle it according to applicable contractual and legal requirements.'
                ],
            ],
            [
                'title' => '7. Data Retention',
                'body' => [
                    'We retain information for as long as reasonably necessary for the purposes described in this Policy, including to provide Services, maintain records, resolve disputes, enforce agreements, and satisfy legal obligations.',
                    'Retention periods may vary depending on the type and sensitivity of information.'
                ],
            ],
            [
                'title' => '8. Security',
                'body' => [
                    'We use reasonable administrative, technical, and organizational measures intended to protect information against unauthorized access, alteration, disclosure, and destruction.',
                    'No internet-based service can guarantee absolute security. If you believe your account or information has been compromised, please contact us promptly.'
                ],
            ],
            [
                'title' => '9. Your Rights',
                'body' => [
                    'Depending on where you live and the laws that apply to you, you may have rights relating to access, correction, deletion, portability, restriction, objection, or withdrawal of consent.',
                    'To exercise an applicable privacy right, contact us using the contact information provided on our website. We may need to verify your identity before completing certain requests.'
                ],
            ],
            [
                'title' => '10. International Processing',
                'body' => [
                    'Senflux and our service providers may process information in countries other than the country where you live.',
                    'Where required, we use appropriate safeguards for cross-border transfers of personal information.'
                ],
            ],
            [
                'title' => '11. Children',
                'body' => [
                    'The Services are not intended for children where their use would be prohibited by applicable law. We do not knowingly collect personal information from children in violation of applicable requirements.'
                ],
            ],
            [
                'title' => '12. Policy Updates',
                'body' => [
                    'We may update this Privacy Policy periodically. The updated version will be published with a revised effective or update date.'
                ],
            ],
        ];
    }


    private function cookieSections(): array
    {
        return [
            [
                'title' => '1. What Are Cookies?',
                'body' => [
                    'Cookies are small files placed on your device when you visit a website. Similar technologies, including pixels, local storage, and device identifiers, may serve comparable purposes.',
                    'These technologies help websites remember preferences, understand usage, maintain sessions, and improve functionality.'
                ],
            ],
            [
                'title' => '2. How Senflux Uses Cookies',
                'body' => [
                    'Senflux may use cookies and similar technologies to maintain authentication sessions, remember preferences, protect against abuse, understand website performance, and measure how visitors interact with our websites.',
                    'Some cookies may be placed by trusted service providers that help us operate the website.'
                ],
            ],
            [
                'title' => '3. Essential Cookies',
                'body' => [
                    'Some cookies are necessary for the website or application to function. These may support authentication, security, session management, form functionality, and other core features.',
                    'Because these technologies are required for essential functionality, disabling them may affect the operation of the Services.'
                ],
            ],
            [
                'title' => '4. Analytics and Performance',
                'body' => [
                    'Where enabled, analytics technologies help us understand aggregate usage patterns, page performance, traffic sources, and areas where the user experience can be improved.',
                    'Analytics information is generally evaluated in aggregate rather than used to identify individual users unnecessarily.'
                ],
            ],
            [
                'title' => '5. Managing Cookies',
                'body' => [
                    'Most browsers allow you to control cookies through browser settings. You may be able to block, delete, or receive warnings about cookies.',
                    'Blocking certain cookies may affect login, preferences, or other website functionality.'
                ],
            ],
            [
                'title' => '6. Third-Party Technologies',
                'body' => [
                    'Some services integrated into our websites may use their own cookies or similar technologies. Their use is governed by the respective provider’s policies.',
                    'We encourage users to review the privacy and cookie practices of third-party providers where relevant.'
                ],
            ],
            [
                'title' => '7. Changes',
                'body' => [
                    'We may update this Cookie Policy as our technology, services, or legal obligations change.'
                ],
            ],
        ];
    }


    private function securitySections(): array
    {
        return [
            [
                'title' => '1. Security Philosophy',
                'body' => [
                    'Senflux treats security as an ongoing engineering and operational responsibility rather than a one-time feature.',
                    'Our approach focuses on reducing unnecessary access, protecting sensitive systems, monitoring infrastructure, maintaining reliable controls, and responding to issues quickly.'
                ],
            ],
            [
                'title' => '2. Infrastructure Security',
                'body' => [
                    'We use modern infrastructure and security practices designed to protect application services, databases, internal systems, and communications.',
                    'Controls may include network segmentation, access controls, encrypted communications, secure configuration practices, monitoring, logging, backups, and controlled deployment processes.'
                ],
            ],
            [
                'title' => '3. Access Control',
                'body' => [
                    'Access to internal systems is limited according to operational requirements and the principle of least privilege.',
                    'Where appropriate, administrative access is protected using strong authentication and additional controls.'
                ],
            ],
            [
                'title' => '4. Data Protection',
                'body' => [
                    'We use appropriate technical and organizational safeguards intended to protect information while it is being transmitted, stored, and processed.',
                    'Sensitive credentials and secrets are managed using appropriate secret-management practices and are not intended to be embedded directly into application source code.'
                ],
            ],
            [
                'title' => '5. Monitoring and Incident Response',
                'body' => [
                    'We monitor relevant systems and operational signals to identify unusual activity, service degradation, and potential security events.',
                    'When a security incident is identified, we assess its scope and take reasonable steps to contain, investigate, remediate, and communicate the incident where required.'
                ],
            ],
            [
                'title' => '6. Secure Development',
                'body' => [
                    'Security considerations are incorporated into development and deployment processes. This may include code review, dependency management, validation of user input, authorization controls, and testing of security-sensitive functionality.'
                ],
            ],
            [
                'title' => '7. Responsible Disclosure',
                'body' => [
                    'If you believe you have identified a security vulnerability affecting Senflux, please contact us privately rather than publicly disclosing exploit details.',
                    'Please provide enough information for our team to understand and reproduce the issue, including affected functionality, relevant steps, and potential impact.'
                ],
            ],
            [
                'title' => '8. Third-Party Risk',
                'body' => [
                    'Senflux may rely on third-party infrastructure and service providers. We evaluate providers based on factors appropriate to their role and the information or systems they handle.'
                ],
            ],
        ];
    }


    private function disclosureSections(): array
    {
        return [
            [
                'title' => '1. General Information',
                'body' => [
                    'Senflux provides software, analytics, data, research, and information relating to blockchain networks, digital assets, market activity, participation, liquidity, and related ecosystem behavior.',
                    'Information presented through Senflux is provided for informational and analytical purposes.'
                ],
            ],
            [
                'title' => '2. Not Investment Advice',
                'body' => [
                    'Nothing on the Senflux website, platform, dashboard, API, research, communication, alert, score, classification, visualization, or other Service constitutes investment advice, financial advice, trading advice, or a recommendation to buy, sell, hold, or otherwise transact in any asset.',
                    'Users should perform their own research and consult appropriately qualified professional advisers before making financial decisions.'
                ],
            ],
            [
                'title' => '3. Market Risk',
                'body' => [
                    'Digital asset and financial markets can be highly volatile and may involve substantial risk of loss.',
                    'Historical behavior, participation metrics, formation signals, liquidity data, or other analytical indicators do not guarantee future market performance.',
                    'A market signal may be correct, incorrect, delayed, incomplete, or interpreted differently depending on context.'
                ],
            ],
            [
                'title' => '4. Formation Intelligence',
                'body' => [
                    'Senflux focuses on identifying patterns associated with participation, persistence, liquidity, capital movement, and market formation.',
                    'A formation score, state, ranking, or signal represents an analytical interpretation of available data. It should not be understood as a prediction or guarantee of future price movement.'
                ],
            ],
            [
                'title' => '5. Data Accuracy',
                'body' => [
                    'Blockchain, market, liquidity, and third-party data may contain errors, omissions, delays, re-organizations, outages, or inconsistencies.',
                    'Senflux uses processes intended to improve data quality, but we do not guarantee that every data point or analytical output is complete, accurate, timely, or uninterrupted.'
                ],
            ],
            [
                'title' => '6. Third-Party Information',
                'body' => [
                    'The Services may incorporate or reference data from third-party sources. Senflux does not guarantee the accuracy, availability, legality, or completeness of third-party information.',
                    'References to third-party projects, assets, protocols, companies, or services do not constitute endorsements unless explicitly stated.'
                ],
            ],
            [
                'title' => '7. No Guarantee of Results',
                'body' => [
                    'Senflux does not guarantee any particular trading, investment, business, or financial outcome from the use of our Services.',
                    'Users remain solely responsible for decisions made using Senflux information.'
                ],
            ],
            [
                'title' => '8. Conflicts and Interests',
                'body' => [
                    'Where applicable, Senflux may have commercial relationships, partnerships, subscriptions, or other interests involving technology providers, data sources, protocols, or ecosystem participants.',
                    'Such relationships do not change the underlying requirement that users independently evaluate information presented through the Services.'
                ],
            ],
            [
                'title' => '9. Availability',
                'body' => [
                    'Service availability may be affected by infrastructure failures, blockchain network conditions, data-provider outages, maintenance, security events, market conditions, or circumstances outside Senflux’s reasonable control.'
                ],
            ],
        ];
    }
}
