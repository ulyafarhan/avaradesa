import { describe, it, expect, vi } from 'vitest';
import { compressImageToWebP } from './imageCompressor';

describe('imageCompressor Utility', () => {
    it('should immediately resolve and return the original file if it is not an image', async () => {
        const pdfFile = new File(['dummy content'], 'document.pdf', { type: 'application/pdf' });
        
        const result = await compressImageToWebP(pdfFile);
        
        expect(result).toBe(pdfFile);
        expect(result.name).toBe('document.pdf');
        expect(result.type).toBe('application/pdf');
    });

    it('should return a Promise when processing files', () => {
        const mockFile = new File(['dummy image'], 'photo.jpg', { type: 'image/jpeg' });
        const result = compressImageToWebP(mockFile);
        
        expect(result).toBeInstanceOf(Promise);
    });
});
