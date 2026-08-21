<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>403 — Access denied</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #0a0b0f;
    --bg-soft: #101219;
    --text: #e6e8ee;
    --muted: #8b93a7;
    --accent-a: #fb7185;
    --accent-b: #8b5cf6;
    --border: rgba(255,255,255,0.08);
  }
  * { box-sizing: border-box; }
  html, body {
    height: 100%;
    margin: 0;
  }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 24px;
    position: relative;
    overflow: hidden;
  }
  body::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 900px;
    height: 900px;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, #fb718522 0%, #8b5cf614 35%, transparent 70%);
    filter: blur(40px);
    pointer-events: none;
  }
  body::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
      linear-gradient(var(--border) 1px, transparent 1px),
      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 48px 48px;
    -webkit-mask-image: radial-gradient(circle at 50% 40%, black, transparent 65%);
    mask-image: radial-gradient(circle at 50% 40%, black, transparent 65%);
    opacity: 0.5;
    pointer-events: none;
  }
  .wrap {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 520px;
  }
  .code {
    font-size: clamp(72px, 14vw, 128px);
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
    margin: 0 0 20px;
    background: linear-gradient(135deg, #fb7185, #8b5cf6);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }
  h1 {
    font-size: clamp(20px, 3vw, 26px);
    font-weight: 700;
    margin: 0 0 12px;
    letter-spacing: -0.01em;
  }
  p {
    font-size: 15px;
    line-height: 1.6;
    color: var(--muted);
    margin: 0 0 28px;
  }
  .brand {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: var(--muted);
    opacity: 0.6;
  }
  .brand span {
    background: linear-gradient(135deg, #fb7185, #8b5cf6);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }
</style>
</head>
<body>
  <div class="wrap">
    <div class="code">403</div>
    <h1>Access denied</h1>
    <p>You don't have permission to view this page.</p>
    <div class="brand">sen<span>flux</span>.ai</div>
  </div>
</body>
</html>