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
            'effective' => 'August 25, 2026',
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
                'title' => '1. About Senflux',
                'body' => [
                    'Senflux is a technology platform focused on Capital Intelligence and Automated Deployment within the digital-asset ecosystem.',
                    'The platform is designed to analyze observable on-chain and market activity, identify potentially meaningful market formations, evaluate qualifying conditions, and facilitate automated deployment when the platform\'s defined conditions are met.',
                    'Senflux currently operates within the Solana ecosystem.',
                    'Senflux\'s technology, software, systems, interfaces, algorithms, models, methodologies, branding, and related intellectual property are proprietary to Senflux or its applicable licensors.',
                ],
            ],
            [
                'title' => '2. Eligibility',
                'body' => [
                    'You may use the Services only if you are legally capable of entering into a binding agreement, meet the minimum age requirement applicable in your jurisdiction, and your use of the Services is lawful in the jurisdiction in which you reside.',
                    'You must not be subject to applicable sanctions or restrictions, and must not be prohibited from participating in digital-asset or related activities under applicable law. All information you provide to Senflux must be accurate and complete.',
                    'Senflux may restrict, suspend, or terminate access where it reasonably believes that a user does not satisfy applicable eligibility requirements.',
                    'You are responsible for determining whether participation in the Services is lawful in your jurisdiction.',
                ],
            ],
            [
                'title' => '3. Important Risk Disclosure',
                'body' => [
                    'Digital assets and automated market participation involve substantial risk. You may lose some or all of the capital you make available through the Services.',
                    'Historical performance is not a guarantee, representation, or promise of future performance. Senflux does not guarantee profits, returns, a particular performance level, preservation of capital, successful execution of every deployment, that every identified formation will mature, that every deployment will be profitable, or that losses will not occur.',
                    'Market conditions, liquidity, blockchain conditions, asset prices, execution conditions, and other factors can change rapidly. You should not commit capital that you cannot afford to lose.',
                    'Nothing on the Senflux website or platform constitutes individualized financial, investment, tax, or legal advice.',
                ],
            ],
            [
                'title' => '4. Formation Packs',
                'body' => [
                    'Senflux may offer Formation Packs that provide different levels of platform participation, intelligence coverage, capital capacity, and participation duration.',
                    'Senflux may modify Formation Pack structures, capacities, fees, durations, or other characteristics in accordance with these Terms and applicable law.',
                    'The terms applicable to a participant are those displayed and accepted at the time of activation, subject to subsequent changes that apply prospectively.',
                ],
            ],
            [
                'title' => '5. Formation Pack Fees',
                'body' => [
                    'Current Formation Pack activation fees are: Scout — $50, Vanguard — $99, Dominion — $249.',
                    'Formation Pack fees are separate from participant capital. Unless expressly stated otherwise at the time of purchase, the Formation Pack fee is not deducted from deployed capital and does not represent a percentage of trading profits.',
                    'Fees may be changed prospectively by Senflux.',
                ],
            ],
            [
                'title' => '6. Capital Capacity',
                'body' => [
                    'A Formation Pack\'s stated capital amount represents the applicable capital capacity, not necessarily the amount a participant must initially fund.',
                    'For example, a Scout participant may activate with the applicable minimum and subsequently increase eligible active capital up to the Scout capacity.',
                    'Capital capacity does not constitute a promise that Senflux will deploy the full capacity or generate a particular level of performance.',
                ],
            ],
            [
                'title' => '7. Top-Ups',
                'body' => [
                    'Where enabled by the platform, participants may add eligible capital to an active Formation Pack during its Formation Cycle, subject to the applicable Formation Pack capacity, platform requirements, applicable compliance requirements, and any other conditions communicated by Senflux.',
                    'Unless Senflux expressly states otherwise, a top-up does not create another Formation Pack, does not create another participation slot, does not reset the Formation Cycle, and does not change the original maturity date.',
                ],
            ],
            [
                'title' => '8. Pack Upgrades',
                'body' => [
                    'A participant does not automatically move to a higher Formation Pack merely by adding capital. To upgrade, the participant must purchase or activate the applicable new Formation Pack.',
                    'Where permitted, eligible active capital from the existing Formation Pack may be transferred to the new Formation Pack. The upgrade process may include: new pack activated, eligible capital transferred, minimum satisfied, previous pack closed, new cycle begins.',
                    'The new Formation Pack may have a new capital capacity, intelligence coverage, participation structure, Formation Cycle, and maturity date. Senflux may determine eligibility and operational procedures for capital transfers.',
                ],
            ],
            [
                'title' => '9. One Active Formation Pack',
                'body' => [
                    'The standard Senflux structure is designed around one active Formation Pack per participant.',
                    'Senflux may restrict or reject attempts to maintain multiple overlapping active Formation Packs where doing so conflicts with the platform structure.',
                ],
            ],
            [
                'title' => '10. Formation Cycle and Capital Lock',
                'body' => [
                    'Once capital is activated within a Formation Pack, the applicable capital is committed to the Formation Cycle. Participating capital is generally locked for the duration of the applicable Formation Cycle.',
                    'During the Formation Cycle, the participant may not ordinarily withdraw the underlying participating capital before maturity except through an early-exit process expressly made available by Senflux.',
                    'The applicable Formation Cycle begins according to the platform\'s activation records.',
                ],
            ],
            [
                'title' => '11. Profits and Withdrawals',
                'body' => [
                    'Where the platform permits profit withdrawals during an active Formation Cycle, eligible profits may be made available for withdrawal subject to the applicable platform rules, processing requirements, and minimum withdrawal requirements.',
                    'The availability of profits for withdrawal does not mean that the underlying participating capital becomes freely withdrawable.',
                    'Participants should review the withdrawal interface and applicable rules before initiating a withdrawal.',
                ],
            ],
            [
                'title' => '12. Early Withdrawal',
                'body' => [
                    'Where Senflux permits early withdrawal of participating capital before maturity, an early-exit fee of 10% may apply to the applicable withdrawal amount.',
                    'The exact calculation, minimums, processing requirements, and treatment of the remaining Formation Pack will be displayed or otherwise communicated through the platform\'s applicable withdrawal process.',
                    'Senflux reserves the right to suspend or restrict early withdrawals where required by security considerations, blockchain conditions, liquidity constraints, compliance requirements, technical issues, force majeure events, or applicable law.',
                    'Important: An early withdrawal may affect the participant\'s Formation Pack status and may result in closure or termination of the applicable Formation Cycle.',
                ],
            ],
            [
                'title' => '13. Maturity',
                'body' => [
                    'At the end of the applicable Formation Cycle, eligible participating capital becomes available according to the platform\'s maturity and withdrawal procedures.',
                    'Depending on the applicable Formation Pack and platform rules, a participant may be able to withdraw, renew, upgrade, or redeploy.',
                    'A new Formation Cycle requires a new or renewed Formation Pack or other applicable activation.',
                ],
            ],
            [
                'title' => '14. Automated Deployment',
                'body' => [
                    'Senflux may use automated systems, algorithms, models, smart-contract infrastructure, blockchain transactions, and other technology to facilitate deployment. "Automated Deployment" means that qualifying deployment actions may be initiated or executed by Senflux technology according to predefined system conditions.',
                    'Automated deployment does not mean every opportunity will be profitable, every transaction will execute at the expected price, every transaction will succeed, losses cannot occur, or that human or system intervention will never be required.',
                    'Blockchain transactions may be affected by network congestion, liquidity, slippage, transaction fees, smart-contract behavior, technical failures, and other conditions outside Senflux\'s control.',
                ],
            ],
            [
                'title' => '15. Capital Intelligence',
                'body' => [
                    'Senflux may analyze observable market and on-chain information including wallet activity, capital concentration, liquidity movement, participation patterns, wallet clusters, formation strength, capital rotation, historical wallet behavior, market conditions, and other publicly observable blockchain or market information.',
                    'Senflux may use this information to generate internal signals, scores, classifications, or deployment decisions.',
                    'No individual signal, score, or classification should be interpreted as a guarantee of future performance.',
                ],
            ],
            [
                'title' => '16. Formation Quality',
                'body' => [
                    'Senflux may evaluate formations using multiple factors, which may include capital quality, liquidity, migration, participation growth, persistence, and wallet behavior.',
                    'A formation may qualify for deployment only when applicable system conditions are satisfied.',
                    'A qualifying formation may subsequently weaken, fail to mature, become illiquid, or produce losses.',
                ],
            ],
            [
                'title' => '17. Historical Performance',
                'body' => [
                    'Senflux may publish historical performance ranges. Where applicable, current illustrative historical monthly ranges may include: Scout 18–20%, Vanguard 20–24%, Dominion 26–30%.',
                    'These figures are historical ranges only. They are not guaranteed returns, fixed monthly income, promised profits, guaranteed yields, minimum returns, or forecasts of future performance.',
                    'Actual results may differ materially.',
                ],
            ],
            [
                'title' => '18. No Guarantee of Performance',
                'body' => [
                    'Senflux expressly disclaims any representation that a participant will achieve historical performance figures or any particular financial result.',
                    'Past performance does not reliably predict future results. Participants remain responsible for evaluating their own tolerance for risk.',
                ],
            ],
            [
                'title' => '19. Digital-Asset and Blockchain Risks',
                'body' => [
                    'Participation in digital-asset markets involves risks including, without limitation, extreme price volatility, liquidity risk, slippage, market manipulation, smart-contract vulnerabilities, blockchain outages, and network congestion.',
                    'Additional risks include transaction failures, protocol changes, cyberattacks, loss or theft of digital assets, private-key compromise, regulatory changes, counterparty risk, and technological failure.',
                    'Senflux cannot eliminate these risks.',
                ],
            ],
            [
                'title' => '20. Fees, Network Costs and Transaction Costs',
                'body' => [
                    'Participants may incur fees associated with Formation Pack activation, blockchain transactions, network fees, deployment, withdrawal, early withdrawal, third-party infrastructure, or other applicable platform services.',
                    'Applicable fees will be communicated through the platform where reasonably practicable.',
                    'Blockchain network fees may change without notice because they are determined by network conditions.',
                ],
            ],
            [
                'title' => '21. Referral and Leadership Programs',
                'body' => [
                    'Senflux may provide referral commissions, rank advancement bonuses, matching bonuses, and other leadership incentives. Current compensation structures may include commissions across multiple levels and other qualifying rewards.',
                    'Participation in compensation programs is subject to qualification requirements, rank requirements, applicable policies, compliance requirements, legitimate referral activity, and any additional terms governing the applicable program.',
                    'Compensation is not guaranteed merely because a participant joins Senflux or purchases a Formation Pack.',
                ],
            ],
            [
                'title' => '22. No Guarantee of Referral Income',
                'body' => [
                    'Referral and leadership compensation depends on actual qualifying activity.',
                    'Participants must not make income guarantees or misleading earnings claims when promoting Senflux.',
                ],
            ],
            [
                'title' => '23. Marketing and Representations',
                'body' => [
                    'Participants and leaders may not guarantee returns, describe historical performance as guaranteed, promise fixed monthly income, misrepresent Senflux\'s technology, make false claims regarding risk, fabricate testimonials or performance results, or make statements inconsistent with Senflux\'s approved materials.',
                    'Senflux may suspend or terminate accounts associated with prohibited promotional conduct.',
                ],
            ],
            [
                'title' => '24. Company and Corporate Information',
                'body' => [
                    'The company\'s current registered jurisdiction and legal entity information may be provided through the website\'s corporate information or legal notice.',
                    'Registration or incorporation in a particular jurisdiction does not by itself constitute regulatory approval, endorsement, or authorization to provide regulated financial services in every jurisdiction.',
                    'Participants are responsible for complying with laws applicable to them.',
                ],
            ],
            [
                'title' => '25. Intellectual Property',
                'body' => [
                    'All Senflux content and technology — including trademarks, logos, software, source code, interfaces, designs, databases, algorithms, models, methodologies, documentation, videos, graphics, text, and other proprietary materials — are owned by or licensed to Senflux and are protected by applicable intellectual-property laws.',
                    'You may not reproduce, modify, distribute, reverse engineer, sell, or commercially exploit Senflux intellectual property without prior written authorization.',
                ],
            ],
            [
                'title' => '26. Account Security',
                'body' => [
                    'You are responsible for maintaining the security of your account credentials and any authentication methods associated with your account.',
                    'You must immediately notify Senflux if you suspect unauthorized access, credential compromise, suspicious activity, or unauthorized transactions.',
                    'Senflux will not be responsible for losses arising from your failure to maintain appropriate account security, except to the extent liability cannot lawfully be excluded.',
                ],
            ],
            [
                'title' => '27. Prohibited Use',
                'body' => [
                    'You agree not to use the Services to violate applicable law, commit fraud, launder money, finance prohibited activities, manipulate markets, or impersonate another person.',
                    'You also agree not to interfere with the platform, introduce malicious software, exploit security vulnerabilities, reverse engineer proprietary technology, circumvent platform controls, or engage in activity that could reasonably expose Senflux or other users to legal, regulatory, or security risk.',
                ],
            ],
            [
                'title' => '28. Compliance and Transaction Monitoring',
                'body' => [
                    'Senflux may implement identity verification, transaction monitoring, sanctions screening, source-of-funds checks, and other compliance procedures where appropriate.',
                    'Senflux may delay, reject, restrict, or suspend transactions while conducting compliance or security reviews.',
                    'Where required by law, Senflux may cooperate with competent authorities.',
                ],
            ],
            [
                'title' => '29. Suspension and Termination',
                'body' => [
                    'Senflux may suspend or terminate access to the Services where it reasonably determines that these Terms have been violated, fraudulent or prohibited activity has occurred, security is at risk, legal or regulatory requirements require action, the account presents material operational risk, or continued access is otherwise inappropriate.',
                    'Termination does not automatically eliminate obligations that accrued before termination.',
                    'Treatment of any remaining capital following suspension or termination will be handled according to applicable platform rules, contractual rights, and applicable law.',
                ],
            ],
            [
                'title' => '30. Service Availability',
                'body' => [
                    'Senflux aims to maintain reliable access to the Services but does not guarantee uninterrupted availability.',
                    'The platform may occasionally be unavailable because of maintenance, upgrades, technical failures, blockchain network conditions, cybersecurity incidents, third-party infrastructure, force majeure events, or other circumstances beyond Senflux\'s reasonable control.',
                ],
            ],
            [
                'title' => '31. Third-Party Services',
                'body' => [
                    'The Services may interact with third-party services, blockchain protocols, wallets, infrastructure providers, exchanges, data providers, or other external systems.',
                    'Senflux does not necessarily control those third parties and is not responsible for their independent actions, policies, availability, or security. Third-party services may have their own terms and privacy policies.',
                ],
            ],
            [
                'title' => '32. Privacy',
                'body' => [
                    'Senflux\'s collection and use of personal information is governed by its applicable Privacy Policy, which forms part of these Terms by reference.',
                    'By using the Services, you acknowledge that Senflux may process information necessary to provide, secure, and administer the Services in accordance with applicable privacy requirements.',
                ],
            ],
            [
                'title' => '33. Disclaimers',
                'body' => [
                    'To the maximum extent permitted by law, the Services are provided on an "as is" and "as available" basis. Senflux disclaims warranties, express or implied, including warranties of merchantability, fitness for a particular purpose, non-infringement, availability, accuracy, and uninterrupted operation.',
                    'Senflux does not warrant that the platform will always operate without interruption, that information will always be accurate or complete, that every transaction will execute successfully, that every formation identified will be profitable, or that the Services will meet every user\'s expectations.',
                ],
            ],
            [
                'title' => '34. Limitation of Liability',
                'body' => [
                    'To the maximum extent permitted by applicable law, Senflux and its officers, directors, employees, affiliates, contractors, and service providers will not be liable for indirect, incidental, consequential, special, exemplary, or punitive damages arising from or related to your use of the Services.',
                    'This includes, without limitation, losses arising from market movements, digital-asset price changes, failed or delayed transactions, blockchain network failures, smart-contract vulnerabilities, liquidity conditions, slippage, unauthorized account access, third-party services, or loss of anticipated profits.',
                    'Nothing in these Terms excludes liability that cannot legally be excluded or limited.',
                ],
            ],
            [
                'title' => '35. Indemnification',
                'body' => [
                    'To the maximum extent permitted by law, you agree to indemnify and hold harmless Senflux and its officers, directors, employees, affiliates, and service providers from claims, liabilities, damages, losses, and expenses arising from your violation of these Terms, your unlawful use of the Services, your violation of another person\'s rights, your unauthorized representations about Senflux, or your fraudulent, negligent, or abusive conduct.',
                ],
            ],
            [
                'title' => '36. Changes to These Terms',
                'body' => [
                    'Senflux may update these Terms from time to time. Updated Terms will become effective when posted to the website unless otherwise stated.',
                    'Your continued use of the Services after updated Terms become effective constitutes acceptance of the revised Terms to the extent permitted by law.',
                ],
            ],
            [
                'title' => '37. Governing Law and Dispute Resolution',
                'body' => [
                    'These Terms and any dispute arising from or relating to the Services shall be governed by the laws of the Republic of the Marshall Islands, without regard to conflict-of-law principles.',
                    'Any dispute, claim, or controversy arising out of or relating to these Terms or the Services shall, to the extent permitted by applicable law, be resolved through confidential and binding arbitration in accordance with the applicable laws of the Republic of the Marshall Islands.',
                    'Nothing in this Section prevents Senflux from seeking urgent or injunctive relief from a court of competent jurisdiction where necessary to protect its rights, property, systems, or confidential information.',
                ],
            ],
            [
                'title' => '38. Severability',
                'body' => [
                    'If any provision of these Terms is determined to be invalid, illegal, or unenforceable, the remaining provisions will remain in full force and effect.',
                    'The invalid provision will be modified to the minimum extent necessary to make it enforceable while preserving its original intent.',
                ],
            ],
            [
                'title' => '39. No Waiver',
                'body' => [
                    'Failure by Senflux to enforce any provision of these Terms does not constitute a waiver of its right to enforce that provision in the future.',
                ],
            ],
            [
                'title' => '40. Entire Agreement',
                'body' => [
                    'These Terms, together with the Privacy Policy and any additional terms expressly incorporated into the Services, constitute the entire agreement between you and Senflux concerning your use of the Services, unless a separate written agreement expressly applies.',
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