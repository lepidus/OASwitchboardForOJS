describe('Send P1-PIO message with success', function () {
    it('Check DOI Configuration', function() {
		cy.login('dbarnes', null, 'publicknowledge');

		cy.get('a:contains("Website")').click();

		cy.get('button#plugins-button').click();

		cy.get('input[id^=select-cell-doipubidplugin]').check();
		cy.get('input[id^=select-cell-doipubidplugin]').should('be.checked');

		cy.get('tr#component-grid-settings-plugins-settingsplugingrid-category-pubIds-row-doipubidplugin a.show_extras').click();
		cy.get('a[id^=component-grid-settings-plugins-settingsplugingrid-category-pubIds-row-doipubidplugin-settings-button]').click();

		cy.get('input#enableIssueDoi').check();
		cy.get('input#enablePublicationDoi').check();
		cy.get('input#enableRepresentationDoi').check();
		
		cy.get('input[name=doiPrefix]').focus().clear().type('10.1234');

		cy.get('form#doiSettingsForm button:contains("Save")').click();
		cy.get('div:contains("Your changes have been saved.")');
	});

    it('Install Funding Plugin', function () {
        cy.login('admin', 'admin', 'publicknowledge');
        cy.contains('a', 'Website').click();
        cy.get('#plugins-button').click();
        cy.get('#pluginGallery-button').click();
        cy.contains('a', 'Funding').click();
        cy.get('[id^=pluginGallery-installPlugin-button-]').click();
        cy.get('.ok').click();
    }) 

    it('Enable Funding Plugin', function () {
        cy.login('dbarnes', null, 'publicknowledge');
        cy.contains('a', 'Website').click();
        cy.get('#plugins-button').click();
        cy.get('input[id^=select-cell-FundingPlugin]').check();
        cy.get('input[id^=select-cell-FundingPlugin]').should('be.checked');
    })

    it('Send Message to OA Switchboard', function () {
        cy.login('dbarnes', null, 'publicknowledge');
        cy.get('#archive-button').click();
        cy.get('#archive > .submissionsListPanel > .listPanel > .listPanel__body > .listPanel__items > .listPanel__itemsList > :nth-child(2) > .listPanel__item--submission > .listPanel__itemSummary > .listPanel__itemActions > .pkpButton').click();
        cy.get('#publication-button').click();
        cy.get('button:contains("Unpublish")').click();
        cy.get('.modal__content button:contains("Unpublish")').click();
        cy.get('#identifiers-button').click();
        cy.get('.pkpFormField__control > .pkpButton').click();
        cy.get('#identifiers > .pkpForm > .pkpFormPages > .pkpFormPage > .pkpFormPage__footer > .pkpFormPage__buttons > .pkpButton').click();

        cy.get('.pkpPublication > .pkpHeader > .pkpHeader__actions > button.pkpButton').contains("Schedule For Publication").click();
        cy.get('.pkpFormPage__footer button:contains("Publish")').click();

        cy.get('.app__notifications').contains("At least one author should have a ROR ID linked to their affiliation (requires the ROR plugin) for the message to be sent to the affiliation.");
        cy.get('.app__notifications').contains("The message was successfully sent to the OA Switchboard");
    })
})