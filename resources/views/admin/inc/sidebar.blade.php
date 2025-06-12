<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
      
           
      <li class="nav-item">
        <a href="{{route('admin.dashboard')}}" class="nav-link  {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Dashboard
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('alladmin')}}" class="nav-link {{ (request()->is('admin/new-admin*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Admins
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('allUser')}}" class="nav-link {{ (request()->is('admin/new-user*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Users
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('allStaff')}}" class="nav-link {{ (request()->is('admin/staff*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Staffs
          </p>
        </a>
      </li>

      <li class="nav-item dropdown {{ (request()->is('admin/service-bookings*') || request()->is('admin/new-service-bookings*') || request()->is('admin/processing-service-bookings*') || request()->is('admin/completed-service-bookings*') || request()->is('admin/cancelled-service-bookings*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/service-bookings*') || request()->is('admin/new-service-bookings*') || request()->is('admin/processing-service-bookings*') || request()->is('admin/completed-service-bookings*') || request()->is('admin/cancelled-service-bookings*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-th"></i>
              <p>
                  Service Bookings
                  <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">    
              <li class="nav-item">
                <a href="{{ route('admin.service.bookings.new') }}" class="nav-link {{ (request()->is('admin/new-service-bookings*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Placed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.service.bookings.processing') }}" class="nav-link {{ (request()->is('admin/processing-service-bookings*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Confirmed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.service.bookings.completed') }}" class="nav-link {{ (request()->is('admin/completed-service-bookings*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Completed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.service.bookings.cancelled') }}" class="nav-link {{ (request()->is('admin/cancelled-service-bookings*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cancelled</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.service.bookings') }}" class="nav-link {{ (request()->is('admin/service-bookings*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>All</p>
                </a>
              </li>
          </ul>
      </li>

      <li class="nav-item dropdown d-none {{ (request()->is('admin/get-new*') || request()->is('admin/get-processing*') || request()->is('admin/job*') || request()->is('admin/get-complete*') || request()->is('admin/get-cancel*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/get-new*') || request()->is('admin/get-processing*') || request()->is('admin/job*') || request()->is('admin/get-complete*') || request()->is('admin/get-cancel*') || Route::is('admin.work.review')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-th"></i>
              <p>
                  Jobs
                  <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
            
              <li class="nav-item">
                <a href="{{ route('admin.job') }}" class="nav-link {{ (request()->is('admin/job*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Create</p>
                </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('admin.new') }}" class="nav-link {{ (request()->is('admin/get-new*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>New Job</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('admin.processing') }}" class="nav-link {{ (request()->is('admin/get-processing*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>In Progress</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('admin.complete') }}" class="nav-link {{ (request()->is('admin/get-complete*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Completed</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('admin.cancel') }}" class="nav-link {{ (request()->is('admin/get-cancel*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Cancelled</p>
                  </a>
              </li>
          </ul>
      </li>

      <li class="nav-item">
        <a href="{{route('allTransactions')}}" class="nav-link {{ (request()->is('admin/all-transaction*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            All Transaction
          </p>
        </a>
      </li>

      <li class="nav-item dropdown {{ (request()->is('admin/mail-content*') || request()->is('admin/mail-content-type*')) ? 'menu-open' : '' }}">
          <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/mail-content*') || request()->is('admin/mail-content-type*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-envelope"></i>
              <p>
                  Mail Content
                  <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a href="{{ route('admin.mail-content-type') }}" class="nav-link {{ (request()->is('admin/mail-content-type*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Mail Content Type</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('admin.mail-content') }}" class="nav-link {{ (request()->is('admin/mail-content*')  && !request()->is('admin/mail-content-type*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Mail Content</p>
                  </a>
              </li>
          </ul>
      </li>

     

      <li class="nav-item">
        <a href="{{route('allUserDeleteReq')}}" class="nav-link {{ (request()->is('admin/user-delete-request*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Account Delete Request
          </p>
        </a>
      </li>
      
      <li class="nav-item">
        <a href="{{route('allcategory')}}" class="nav-link {{ (request()->is('admin/category*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Work Categories
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('allsubcategory')}}" class="nav-link {{ (request()->is('admin/sub-category*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Work Sub Categories
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('alltypes')}}" class="nav-link {{ (request()->is('admin/type*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Types
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('allservices')}}" class="nav-link {{ (request()->is('admin/service*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Services
          </p>
        </a>
      </li>

      <li class="nav-item">
          <a href="{{ route('allslider') }}" class="nav-link {{ (request()->is('admin/slider*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-sliders-h"></i>
              <p>Slider</p>
          </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.location')}}" class="nav-link {{ (request()->is('admin/location*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Service Locations
          </p>
        </a>
      </li>

      <li class="nav-item d-none">
        <a href="{{route('allQuestions')}}" class="nav-link {{ (request()->is('admin/questions*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Review Questions
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{route('admin.companyDetail')}}" class="nav-link {{ (request()->is('admin/company-details*')) ? 'active' : '' }}">
          <i class="nav-icon fas fa-th"></i>
          <p>
            Company Details
          </p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.aboutUs') }}" class="nav-link {{ (request()->is('admin/about-us*')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-info-circle"></i>
            <p>About Us</p>
        </a>
      </li>

      <li class="nav-item">
          <a href="{{ route('admin.privacy-policy') }}" class="nav-link {{ (request()->is('admin/privacy-policy*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-shield-alt"></i>
              <p>Privacy Policy</p>
          </a>
      </li>

      <li class="nav-item">
          <a href="{{ route('admin.homeFooter') }}" class="nav-link {{ (request()->is('admin/home-footer*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-home"></i>
              <p>Hero Content</p>
          </a>
      </li>

      <li class="nav-item dropdown {{ (request()->is('admin/reviews*') || request()->is('admin/quotes*')) ? 'menu-open' : '' }}">
          <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/reviews*') || request()->is('admin/quotes*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-th"></i>
              <p>
                  Feedbacks
                  <i class="fas fa-angle-left right"></i>
              </p>
          </a>
          <ul class="nav nav-treeview"> 
              <li class="nav-item">
                <a href="{{ route('allReviews') }}" class="nav-link {{ (request()->is('admin/reviews*')) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Reviews</p>
                </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('allQuotes') }}" class="nav-link {{ (request()->is('admin/quotes*')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Quotes</p>
                  </a>
              </li>
          </ul>
      </li>

      <li class="nav-item">
          <a href="{{ route('admin.careers.index') }}" class="nav-link {{ (request()->is('admin/careers*')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>Career</p>
          </a>
      </li>

      <li class="nav-item" style="margin-top: 200px">
      </li>
  
    </ul>
  </nav>