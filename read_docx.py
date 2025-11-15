import docx
import sys
import io

# Set UTF-8 encoding for stdout
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def read_docx(filepath):
    doc = docx.Document(filepath)
    full_text = []
    for para in doc.paragraphs:
        full_text.append(para.text)
    return '\n'.join(full_text)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        filepath = sys.argv[1]
        print(read_docx(filepath))
