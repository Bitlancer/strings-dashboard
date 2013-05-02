<section>
    <h1>User Management</h1>
    <div class="columns">
      <div>
		<?php
			echo $this->Strings->buildStringsDatatable(
				'users',					//Table ID
				'Current users',			//Table title
				$userTableColumns,			//Column headings
				$_SERVER['REQUEST_URI'],	//URI for pulling data
				2,							//Page length
				'Create user',				//CTA button txt
				'Create User',				//CTA title
				'#create-user',				//CTA src
				$userTableCTAEnabled		//CTA enabled
			);
		?>
      </div>
    <div>
        <h2>User activity<a class="cta refresh"></a></h2>
        <ul id="activity">
          <li>asears logged in<small>12 minutes ago</small></li>
          <li>jcotton deleted the instance <strong>anarachy</strong><small>about 4 hours ago</small></li>
          <li>mjuszczak edited user permissions for <strong>asears</strong><small>2 days ago</small></li>
          <li>mjuszczak added the user <strong>asears</strong><small>2 days ago</small></li>
        </ul>
    </div>
    <hr class="clear">
  </section>
  <div style="display:none">
    <!-- create user -->
    <div id="create-user">
      <form>
        <fieldset>
          <legend>Name</legend>
          <input type="text" placeholder="first name" />
          <input type="text" placeholder="last name" />
        </fieldset>
        <fieldset>
          <legend>Details</legend>
          <input type="email" placeholder="email address" />
          <input type="text" placeholder="phone number (xxx-xxx-xxxx)" />
        </fieldset>
        <div class="submit">
          <a class="cta primary submit">Confirm</a>
          <a class="cta">Cancel</a>
        </div>
      </form>          
    </div>
    <!-- disable user -->
    <div id="disable-user">
      <form>
        <p>Are you sure you want to disable #{username}?</p>
        <div class="submit">
          <a class="cta primary submit">Confirm</a>
          <a class="cta">Cancel</a>
        </div>
      </form>          
    </div>
    <!-- reset password -->
    <div id="reset-password">
      <form>
          <input type="password" placeholder="new password" />
          <input type="password" placeholder="new password (verify)" />
        <div class="submit">
          <a class="cta primary disabled submit">Reset password</a>
          <a class="cta">Cancel</a>
        </div>
      </form>          
    </div>
    <!-- edit user -->
    <div id="edit-user">
      <form>
        <fieldset>
          <legend>Name</legend>
          <input type="text" placeholder="first name" />
          <input type="text" placeholder="last name" />
        </fieldset>
        <fieldset>
          <legend>Details</legend>
          <input type="email" placeholder="email address" />
          <input type="text" placeholder="phone number (xxx-xxx-xxxx)" />
        </fieldset>
        <div class="submit">
          <a class="cta primary submit">Save</a>
          <a class="cta">Cancel</a>
        </div>
      </form>          
    </div>
    <!-- edit permissions -->
    <div id="edit-permissions">
      <form>
        <fieldset>
          <legend>System</legend>
          <input id="auto-system" class="autocomplete" data-src="/strings/assets/json/autocomplete.json" data-width="548px" />
        </fieldset>
        <fieldset>
          <legend>Infrastructure</legend>
          <input id="auto-infrastructure" class="autocomplete" data-src="/strings/assets/json/autocomplete.json" data-width="548px" />
        </fieldset>
        <fieldset>
          <legend>Application</legend>
          <input id="auto-application" class="autocomplete" data-src="/strings/assets/json/autocomplete.json" data-width="548px" />
        </fieldset>
        <fieldset>
          <legend>User</legend>
          <input id="auto-user" class="autocomplete" data-src="/strings/assets/json/autocomplete.json" data-width="548px" />
        </fieldset>
        <div class="submit">
          <a class="cta primary submit">Save</a>
          <a class="cta">Cancel</a>
        </div>
      </form>          
    </div>
  </div>
