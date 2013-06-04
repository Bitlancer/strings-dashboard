<style>
    .vertical-align-middle {
        vertical-align: middle;
    }
    #add-team {
        width: 90%;
    }
</style>
<fieldset class="association">

    <input type="text" id="add-team" name="team" placeholder="team" />
    <a class="cta primary small">Add</a>

    <table style="margin-top:5px;">
        <thead>
        <tr>
            <th>Team</th>
            <th>Sudo Roles</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td class="vertical-align-middle">Admins</td>
                <td><input id="test" placeholder="ex: Net Admins" data-width="350" class="autocomplete-tag ui-autocomplete-input" data-placeholder="Enter a sudo role name..." data-src="/autocomplete-example.json" autocomplete="off"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span></td>
                <td class="vertical-align-middle"><a class="action">Remove</a></td>
            </tr>
            <tr>
                <td class="vertical-align-middle">Devops</td>
                <td><input id="Test2" placeholder="ex: Net Admins" data-width="350" class="autocomplete-tag ui-autocomplete-input" data-placeholder="Enter a sudo role name..." data-src="/autocomplete-example.json" autocomplete="off"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                </td>
                <td class="vertical-align-middle"><a class="action">Remove</a></td>
            </tr>
        </tbody>
    </table>

</fieldset>
