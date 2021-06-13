const { EOL } = require('os')
const fs = require('fs/promises')

class FFMpegOptionsGenerator {

	codecs = [];
	formats = [];

	async generate() {
		await this.loadFormats()

		await fs.writeFile('formats.json', JSON.stringify(this.formats, null, 4))

		console.log('done')
	}

	async loadFormats() {
		const lines = await fs.readFile('./ffmpeg-formats.txt', 'utf-8')

		this.formats = lines.split(EOL).reduce((formats, line) => {
			const [extensions, ...labelPieces] = line.slice(4).split(' ')
			const label = labelPieces.join(' ').trim()
			extensions.split(',').forEach(extension => {
				formats[extension] = {
					...formats[extension],
					label,
					...(line[1] === 'D' ? { decode: true } : null),
					...(line[2] === 'E' ? { encode: true } : null),
				}
			})

			return formats
		}, {})

		this.formats = Object.entries(this.formats)
		this.formats.sort(([a], [b]) => a.localeCompare(b))
		this.formats = this.formats.map(([extension, { decode = false, encode = false, label = '' }]) => ({
			extension,
			decode,
			encode,
			label,
		})).filter(f => f.label)
	}

}

new FFMpegOptionsGenerator().generate()
