<?php
// Database connection
require_once __DIR__ . '/../../../config/databaseconnection.php';

$skills = [
    'HTML', 'CSS', 'JavaScript', 'PHP', 'Python', 'Laravel', 'React', 'Vue.js',
    'Angular', 'Node.js', 'SQL', 'MySQL', 'PostgreSQL', 'MongoDB', 'Git',
    'UI/UX Design', 'Adobe Photoshop', 'Adobe Illustrator', 'Graphic Design',
    'Data Analysis', 'Excel', 'WordPress', 'SEO', 'Digital Marketing',
    'Content Writing', 'Copywriting', 'Project Management', 'Networking',
    'Cybersecurity', 'Java', 'C++', 'C#', 'Ruby', 'Kotlin', 'Swift',
    'Machine Learning', 'Artificial Intelligence', 'DevOps', 'Docker', 'AWS',
    'TypeScript', 'GraphQL', 'Redis', 'Elasticsearch', 'Firebase', 'Kubernetes',
    'Terraform', 'Ansible', 'Prometheus', 'Jenkins', 'CircleCI', 'GitLab CI',
    'Selenium', 'Puppeteer', 'Rust', 'Go', 'Scala', 'Haskell', 'Erlang',
    'Bash', 'PowerShell', 'Xamarin', 'Flutter', 'React Native', 'Ionic',
    'WebAssembly', 'Blockchain', 'Solidity', 'Smart Contracts', 'CI/CD',
    'Test-Driven Development (TDD)', 'Behavior-Driven Development (BDD)',
    'Agile', 'Scrum', 'Data Engineering', 'ETL', 'Apache Kafka', 'Hadoop',
    'Apache Spark', 'Tableau', 'Penetration Testing', 'Vulnerability Assessment', 'Incident Response',
    'Threat Intelligence', 'SIEM', 'SOC Operations', 'Endpoint Detection and Response (EDR)',
    'Network Security', 'Identity and Access Management (IAM)', 'Malware Analysis',
    'PPC Advertising', 'Social Media Advertising', 'Email Marketing Automation',
    'Conversion Rate Optimization (CRO)', 'Google Analytics 4',
    'Marketing Automation (HubSpot, Marketo)', 'Influencer Marketing',
    'Content Strategy', 'Affiliate Marketing', 'Paid Search (SEM)'
];

$stmt = $conn->prepare("INSERT IGNORE INTO skills (skill_name) VALUES (?)");

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$count = 0;
foreach ($skills as $skill) {
    $stmt->bind_param("s", $skill);
    if ($stmt->execute()) {
        $count++;
    }
}

$stmt->close();
$conn->close();

echo "Seeded {$count} skills successfully.";