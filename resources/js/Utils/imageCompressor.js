export function compressImageToWebP(file, options = {}) {
    const { maxWidth = 1600, maxHeight = 1600, quality = 0.8 } = options;

    if (!file.type.startsWith('image/')) {
        return Promise.resolve(file);
    }

    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = function (event) {
            const img = new Image();
            img.onload = function () {
                let width = img.width;
                let height = img.height;

                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }

                if (height > maxHeight) {
                    width = Math.round((width * maxHeight) / height);
                    height = maxHeight;
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => {
                        if (!blob) {
                            resolve(file);
                            return;
                        }

                        const newFileName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
                        const webpFile = new File([blob], `${newFileName}.webp`, {
                            type: 'image/webp',
                            lastModified: Date.now()
                        });

                        resolve(webpFile);
                    },
                    'image/webp',
                    quality
                );
            };

            img.onerror = function () {
                resolve(file);
            };

            img.src = event.target.result;
        };

        reader.onerror = function () {
            resolve(file);
        };

        reader.readAsDataURL(file);
    });
}
