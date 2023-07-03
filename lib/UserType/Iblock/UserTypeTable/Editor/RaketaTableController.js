class raketaTableController {
	constructor(containerId, obj) {
		this.helper = this.helperFun()

		const tableWrapper = document.getElementById(containerId)
		this.containerId = containerId
		this.emptyTable = '{"HEADER":{"0":"","1":""},"BODY":{"0":{"0":"","1":""}}}'
		this.blocks = {
			tableWrapper,
			tableBody: tableWrapper.querySelector('tbody'),
			tableHead: tableWrapper.querySelector('thead'),
			popup: tableWrapper.querySelector('.js-table-popup'),
			popupCloneBtn: tableWrapper.querySelector('.js-table-popup-close'),
			input: tableWrapper.querySelector('.js-table-input'),

			btmDeleteRow: tableWrapper.querySelector('.js-table-delete-row'),
			btmDeleteColumn: tableWrapper.querySelector('.js-table-delete-column'),

			btnAddColumnLeft: tableWrapper.querySelector('.js-table-add-column-left'),
			btnAddColumnRight: tableWrapper.querySelector('.js-table-add-column-right'),
			btnAddRowTop: tableWrapper.querySelector('.js-table-add-row-top'),
			btnAddRowBottom: tableWrapper.querySelector('.js-table-add-row-bottom'),

			btnCopyTable: tableWrapper.querySelector('.js-table-copy'),
			btnPasteTable: tableWrapper.querySelector('.js-table-paste'),
			btnClearTable: tableWrapper.querySelector('.js-table-clear'),

			additionalOptionsField: [...tableWrapper.querySelectorAll('.js-additional-field-value')],
		}
		this.popupData = {
			tdIndex: -1,
			tdTrIndex: -1,
			open: false,
			tempBlock: []
		}

		this.helper.pasteDataJSON(obj ? obj : this.emptyTable)

		this.reindex()
		this.eventInit()
		this.popupEvent()

	}

	saveData() {
		const tableData = {HEADER: {}, BODY: {}, OPTIONS: {}}

		if (this.blocks.tableHead) {
			const thList = [...this.blocks.tableHead.querySelectorAll('th')]
			if (thList) {
				for (const thIndex in thList) {
					const th = thList[thIndex]
					tableData.HEADER[thIndex] = th.innerHTML
				}
			}
		}

		const trList = [...this.blocks.tableBody.querySelectorAll('tr')]
		for(const indexTr in trList) {
			if (!tableData.BODY[indexTr]) {
				tableData.BODY[indexTr] = {}
			}
			const tdList = [...trList[indexTr].querySelectorAll('td')]
			for (const indexTd in tdList) {
				tableData.BODY[indexTr][indexTd] = tdList[indexTd].innerHTML
			}
		}

		let dataNotEmpty = false
		for (const rowIndex in tableData.BODY) {
			const row = tableData.BODY[rowIndex]
			for (const columnIndex in row) {
				const column = row[columnIndex]
				if (column) dataNotEmpty = true;
			}
		}

		if(dataNotEmpty) {
			for(const option of this.blocks.additionalOptionsField) {
				const dataOptions = {
					name: option.getAttribute('name'),
					value: option.type === 'checkbox' ? option.checked : option.value
				}
				tableData.OPTIONS[dataOptions.name] = dataOptions
			}
		}
		this.blocks.input.value = dataNotEmpty ? JSON.stringify(tableData) : ''
	}

	reindex() {
		const trList = this.blocks.tableWrapper.querySelectorAll('tr');
		for (const trIndex in [...trList]) {
			const trItem = trList[trIndex]
			trItem.dataset.trIndex = trIndex
			const tdTrList = trItem.querySelectorAll('td, th')
			for (const tdIndex in [...tdTrList]) {
				const tdItem = tdTrList[tdIndex]
				tdItem.dataset.tdIndex = tdIndex
				tdItem.dataset.tdTrIndex = trIndex
			}
		}
		this.saveData()
	}

	closePopup() {
		this.popupData.open = false
		this.blocks.popup.classList.remove('_active')
	}

	helperFun() {
		return {
			setDeleteColor: (selector, on) => {
				const tdActiveList = this.blocks.tableBody.querySelectorAll(selector)
				if (tdActiveList) {
					tdActiveList.forEach(item => item.classList.toggle('_delete', on))
				}
			},
			deleteTableItem: (selector) => {
				const list = this.blocks.tableWrapper.querySelectorAll(selector)
				if (list) {
					list.forEach(item => item.remove())
					this.closePopup()
					this.reindex()
				}
			},
			addColumn: (position, addToTemp = false) => {
				const tdList = this.blocks.tableWrapper.querySelectorAll(`[data-td-index="${this.popupData.tdIndex}"]`)
				for (const item of [...tdList]) {
					const td = this.helper.createdBlock('', item.closest('.js-raketa-editor-table-head') ? 'th' : 'td')
					td.classList.add('add')
					item[position](td)
					if (addToTemp) {
						this.popupData.tempBlock.push(td)
					}
				}
			},
			addRow: (position, addToTemp = false) => {
				const activeRow = this.blocks.tableBody.querySelector(`[data-tr-index="${this.popupData.tdTrIndex}"]`)
				const countTd = activeRow.querySelectorAll('td').length;
				const tr = document.createElement('tr')
				for(let i = 0; i < countTd; i++) {
					const td = this.helper.createdBlock('', 'td')
					td.classList.add('add')
					tr.append(td)
					this.popupData.tempBlock.push(td)
				}
				if (addToTemp) {
					this.popupData.tempBlock.push(tr)
				}
				activeRow[position](tr);
			},
			addSuccess: () => {
				this.popupData.tempBlock.forEach(item => item.classList.remove('add'))
				this.popupData.tempBlock = []
				this.reindex()
				this.closePopup()
			},
			addFantom: (nameFun, position, action) => {
				if (action === 'add') {
					this.helper[nameFun](position, true)
					if(nameFun === 'addRow' && position === 'before' && this.popupData.tempBlock[0]) {
						this.blocks.popup.style.top = (+this.blocks.popup.style.top.replace(/\D/g, '') + this.popupData.tempBlock[0].offsetHeight) + 'px'
					}
				} else {
					if(nameFun === 'addRow' && position === 'before' && this.popupData.tempBlock[0]) {
						this.blocks.popup.style.top = (+this.blocks.popup.style.top.replace(/\D/g, '') - this.popupData.tempBlock[0].offsetHeight) + 'px'
					}
					this.popupData.tempBlock.forEach(item => item.remove())
					this.popupData.tempBlock = []
				}
			},
			copyToClipboard: (str) => {
				const el = document.createElement('textarea');
				el.value = str;
				el.style.position = 'absolute';
				el.style.left = '-9999px';
				document.body.appendChild(el);
				el.select();
				document.execCommand('copy');
				document.body.removeChild(el);
			},
			toJSON: (str) => {
				try { return JSON.parse(str) } catch (e) { return false }
			},
			createdBlock: (text, tag) => {
				const block = document.createElement(tag)
				block.innerText = text;
				block.setAttribute('contenteditable', 'true')
				return block;
			},
			pasteData: (data) => {
				if(data && data.HEADER && data.BODY) {

					const tableHeader = this.blocks.tableHead.querySelector('tr')
					tableHeader.innerHTML = ''

					for (const itemIndex in data.HEADER) {
						tableHeader.append(this.helper.createdBlock(data.HEADER[itemIndex], 'th'))
					}

					this.blocks.tableBody.innerHTML = '';
					for (const rowIndex in data.BODY) {
						const blockRow = document.createElement('tr')
						const row = data.BODY[rowIndex]
						for (const columnIndex in row) {
							blockRow.append(this.helper.createdBlock(row[columnIndex], 'td'))
						}
						this.blocks.tableBody.append(blockRow)
					}

					if(data.OPTIONS) {
						for(const optionIndex in data.OPTIONS) {
							const option = data.OPTIONS[optionIndex]
							const optionFieldSelectorInPage = `[name="${option['name']}"].js-additional-field-value`
							const fieldInPage = this.blocks.tableWrapper.querySelector(optionFieldSelectorInPage)
							if(fieldInPage) {
								if(fieldInPage.type === 'checkbox') {
									fieldInPage.checked = option.value
								} else {
									fieldInPage.value = option.value
								}
							}
						}
					} else {
						for(const field of this.blocks.additionalOptionsField) {
							if(field.type === 'checkbox') {
								field.checked = false
							} else {
								field.value = ''
							}
						}
					}

					this.reindex()
					this.saveData()
				} else if (data !== null) {
					alert('Не верный формат данных')
				}
			},
			pasteDataJSON: (tableJson) => {
				const data = this.helper.toJSON(tableJson);
				this.helper.pasteData(data)
			}
		}
	}

	eventInit() {
		this.blocks.popupCloneBtn.addEventListener('click', () => this.closePopup())
		this.blocks.tableWrapper.addEventListener('click', (event) => {
			if (!event.target.closest('.js-table-popup')) {
				this.closePopup()
			}
		})

		document.body.addEventListener('click', (event) => {
			if (
				event.target !== this.blocks.tableWrapper &&
				!this.blocks.tableWrapper.contains(event.target) &&
				this.popupData.open
			) {
				this.closePopup()
			}
		})

		this.blocks.tableWrapper.addEventListener('contextmenu', (event) => {
			this.closePopup()
			const targetCoords = this.blocks.tableWrapper.getBoundingClientRect();
			const xCoord = Math.floor(event.clientX - targetCoords.left);
			const yCoord = Math.floor(event.clientY - targetCoords.top);
			const target = event.target.localName === 'td' ? event.target : event.target.closest('td[contenteditable]')
			if (target) {
				event.preventDefault()
				this.blocks.popup.classList.add('_active')
				this.popupData.open = true
				this.blocks.popup.style.left = xCoord + 'px';
				this.blocks.popup.style.top = yCoord + 'px';
				this.popupData.tdIndex = target.dataset.tdIndex
				this.popupData.tdTrIndex = target.dataset.tdTrIndex
			}
		})

		this.blocks.tableWrapper.addEventListener('input', (event) => {
			const target = event.target.localName === 'td' ? event.target : event.target.closest('td')
			if (target) {
				this.saveData()
			}
		})

		this.blocks.btnCopyTable.addEventListener('click', () => {
			this.helper.copyToClipboard(this.blocks.input.value)
		})

		this.blocks.btnClearTable.addEventListener('click', () => {
			this.helper.pasteDataJSON(this.emptyTable)
			for(const field of this.blocks.additionalOptionsField) {
				field.value = ''
			}
		})

		this.blocks.btnPasteTable.addEventListener('click', () => {
			const tableJson = prompt('Вставте скопированные данные таблицы')
			//this.helper.pasteDataJSON(tableJson)
			const rows = tableJson.split('\r\n')
			const table = { HEADER: [], BODY: [] }
			for (const rowIndex in rows) {
				const row = `${rows[rowIndex]}`.replace('\n', ' ')
				const columns = `${row}`.split('\t')
				if((+rowIndex) === 0) {
					table.HEADER = columns
				} else {
					table.BODY.push(columns)
				}
			}
			this.helper.pasteData(table)
		})

		for(const option of this.blocks.additionalOptionsField) {
			option.addEventListener('input', () => this.saveData())
		}
	}

	popupEvent() {
		// DELETE ROW
		this.blocks.btmDeleteRow.addEventListener('mouseover', () => {
			this.helper.setDeleteColor(`[data-td-tr-index="${this.popupData.tdTrIndex}"]`, true)
		})
		this.blocks.btmDeleteRow.addEventListener('mouseout', () => {
			this.helper.setDeleteColor(`[data-td-tr-index="${this.popupData.tdTrIndex}"]`, false)
		})
		this.blocks.btmDeleteRow.addEventListener('click', () => {
			this.helper.deleteTableItem(`[data-tr-index="${this.popupData.tdTrIndex}"]`)
		})
		// DELETE ROW
		// DELETE COLUMN
		this.blocks.btmDeleteColumn.addEventListener('mouseover', () => {
			this.helper.setDeleteColor(`[data-td-index="${this.popupData.tdIndex}"]`, true)
		})
		this.blocks.btmDeleteColumn.addEventListener('mouseout', () => {
			this.helper.setDeleteColor(`[data-td-index="${this.popupData.tdIndex}"]`, false)
		})
		this.blocks.btmDeleteColumn.addEventListener('click', () => {
			this.helper.deleteTableItem(`[data-td-index="${this.popupData.tdIndex}"]`)
		})
		// DELETE COLUMN
		this.blocks.btnAddColumnLeft.addEventListener('mouseover', () => this.helper.addFantom('addColumn', 'before', 'add'))
		this.blocks.btnAddColumnLeft.addEventListener('mouseout', () => this.helper.addFantom('addColumn', 'before', 'remove'))
		this.blocks.btnAddColumnLeft.addEventListener('click', () => this.helper.addSuccess())

		this.blocks.btnAddColumnRight.addEventListener('mouseover', () => this.helper.addFantom('addColumn', 'after', 'add'))
		this.blocks.btnAddColumnRight.addEventListener('mouseout', () => this.helper.addFantom('addColumn', 'after', 'remove'))
		this.blocks.btnAddColumnRight.addEventListener('click', () => this.helper.addSuccess())

		this.blocks.btnAddRowTop.addEventListener('mouseover', () => this.helper.addFantom('addRow', 'before', 'add'))
		this.blocks.btnAddRowTop.addEventListener('mouseout', () => this.helper.addFantom('addRow', 'before', 'remove'))
		this.blocks.btnAddRowTop.addEventListener('click', () => this.helper.addSuccess())

		this.blocks.btnAddRowBottom.addEventListener('mouseover', () => this.helper.addFantom('addRow', 'after', 'add'))
		this.blocks.btnAddRowBottom.addEventListener('mouseout', () => this.helper.addFantom('addRow', 'after', 'remove'))
		this.blocks.btnAddRowBottom.addEventListener('click', () => this.helper.addSuccess())
	}
}
