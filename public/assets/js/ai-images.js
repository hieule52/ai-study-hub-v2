/**
 * AI Image Generator - JavaScript
 * Using Pollinations.ai free API for image generation
 */

document.addEventListener('DOMContentLoaded', function () {
  // ===== ELEMENTS =====
  const promptInput = document.getElementById('promptInput');
  const generateBtn = document.getElementById('generateBtn');
  const loadingIndicator = document.getElementById('loadingIndicator');
  const resultContainer = document.getElementById('resultContainer');
  const resultImage = document.getElementById('resultImage');
  const resultPrompt = document.getElementById('resultPrompt');
  const sizeSelect = document.getElementById('sizeSelect');
  const styleBtns = document.querySelectorAll('.style-btn');

  let selectedStyle = '';

  // ===== STYLE SELECTION =====
  styleBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      styleBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      selectedStyle = this.getAttribute('data-style');
    });
  });

  // ===== GENERATE IMAGE =====
  generateBtn.addEventListener('click', generateImage);

  // Enter key to generate
  promptInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.ctrlKey) {
      generateImage();
    }
  });

  function generateImage() {
    const prompt = promptInput.value.trim();

    if (!prompt) {
      alert('⚠️ Vui lòng nhập mô tả hình ảnh!');
      return;
    }

    // Show loading
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo...';
    loadingIndicator.style.display = 'block';
    resultContainer.style.display = 'none';

    // Build full prompt with style
    let fullPrompt = prompt;
    if (selectedStyle) {
      fullPrompt += ', ' + selectedStyle;
    }

    // Get size
    const size = sizeSelect.value.split('x'); //"1024x1024" => ["1024", "1024"]
    const width = size[0];
    const height = size[1];

    // Generate image using Pollinations.ai API
    // Free, no API key required!
    const imageUrl = `https://image.pollinations.ai/prompt/${encodeURIComponent(fullPrompt)}?width=${width}&height=${height}&nologo=true&enhance=true`;

    // Create new image to preload
    const img = new Image();
    img.onload = function () {
      // Image loaded successfully
      resultImage.src = imageUrl;
      resultPrompt.innerHTML = `<strong>Mô tả:</strong> ${fullPrompt}`;
      
      // Hide loading, show result
      loadingIndicator.style.display = 'none';
      resultContainer.style.display = 'block';
      
      // Reset button
      generateBtn.disabled = false;
      generateBtn.innerHTML = '<i class="fas fa-magic"></i> Tạo hình ảnh';

      // Scroll to result
      resultContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    img.onerror = function () {
      // Error loading image
      alert('❌ Lỗi khi tạo hình ảnh. Vui lòng thử lại!');
      loadingIndicator.style.display = 'none';
      generateBtn.disabled = false;
      generateBtn.innerHTML = '<i class="fas fa-magic"></i> Tạo hình ảnh';
    };

    // Start loading
    img.src = imageUrl;
  }
});

// ===== HELPER FUNCTIONS =====

function setExample(text) {
  document.getElementById('promptInput').value = text;
  document.getElementById('promptInput').focus();
}

function downloadImage() {
  const img = document.getElementById('resultImage');
  const link = document.createElement('a');
  link.href = img.src;
  link.download = 'ai-generated-image.png';
  link.click();
}

function shareImage() {
  const img = document.getElementById('resultImage');
  
  if (navigator.share) {
    // Use Web Share API if available
    fetch(img.src)
      .then(res => res.blob())
      .then(blob => {
        const file = new File([blob], 'ai-image.png', { type: 'image/png' });
        navigator.share({
          title: 'AI Generated Image',
          text: 'Check out this AI-generated image!',
          files: [file]
        });
      })
      .catch(err => {
        // Fallback: copy link
        copyImageLink();
      });
  } else {
    // Fallback: copy link
    copyImageLink();
  }
}

function copyImageLink() {
  const img = document.getElementById('resultImage');
  navigator.clipboard.writeText(img.src).then(() => {
    alert('✅ Link ảnh đã được copy!');
  });
}
