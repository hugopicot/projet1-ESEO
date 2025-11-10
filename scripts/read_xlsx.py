import sys
import zipfile
from xml.etree import ElementTree as ET

def load_shared_strings(z):
    try:
        with z.open('xl/sharedStrings.xml') as f:
            tree = ET.parse(f)
            root = tree.getroot()
            ns = {'t': root.tag.split('}')[0].strip('{')}
            strings = []
            for si in root.findall('.//{*}si'):
                # Concatenate all text nodes in si (handles rich text)
                text_parts = []
                for t in si.findall('.//{*}t'):
                    text_parts.append(t.text or '')
                strings.append(''.join(text_parts))
            return strings
    except KeyError:
        return []

def col_to_index(col_ref):
    idx = 0
    for c in col_ref:
        if c.isalpha():
            idx = idx * 26 + (ord(c.upper()) - ord('A') + 1)
    return idx - 1 if idx > 0 else 0

def read_sheet_rows(z, sheet_path='xl/worksheets/sheet1.xml', shared_strings=None):
    with z.open(sheet_path) as f:
        tree = ET.parse(f)
        root = tree.getroot()
        rows_out = []
        for row in root.findall('.//{*}row'):
            cells = {}
            for c in row.findall('{*}c'):
                r = c.attrib.get('r', 'A1')
                # Extract column letters
                col_letters = ''.join([ch for ch in r if ch.isalpha()])
                col_idx = col_to_index(col_letters)
                cell_type = c.attrib.get('t', '')
                v_el = c.find('{*}v')
                is_el = c.find('{*}is')  # inlineStr
                val = ''
                if is_el is not None:
                    ts = is_el.findall('.//{*}t')
                    val = ''.join([(t.text or '') for t in ts])
                elif v_el is not None and v_el.text is not None:
                    raw = v_el.text
                    if cell_type == 's':
                        # Shared string index
                        try:
                            idx = int(raw)
                            val = shared_strings[idx] if shared_strings and 0 <= idx < len(shared_strings) else raw
                        except Exception:
                            val = raw
                    else:
                        val = raw
                cells[col_idx] = val
            # Normalize row to list
            if cells:
                max_idx = max(cells.keys())
                row_vals = [cells.get(i, '') for i in range(max_idx + 1)]
                rows_out.append(row_vals)
        return rows_out

def main():
    path = sys.argv[1] if len(sys.argv) > 1 else 'Grille - S1 - PHP.xlsx'
    try:
        with zipfile.ZipFile(path, 'r') as z:
            shared_strings = load_shared_strings(z)
            rows = read_sheet_rows(z, 'xl/worksheets/sheet1.xml', shared_strings)
            # Print as CSV-like output
            for r in rows:
                print('\t'.join([str(x).replace('\n', ' ').strip() for x in r]))
    except FileNotFoundError:
        print(f'File not found: {path}', file=sys.stderr)
        sys.exit(1)
    except zipfile.BadZipFile:
        print('Invalid XLSX file', file=sys.stderr)
        sys.exit(1)

if __name__ == '__main__':
    main()