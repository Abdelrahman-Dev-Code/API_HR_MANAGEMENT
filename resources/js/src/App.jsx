import { useEffect, useMemo, useState } from 'react';

const API_BASE = '/api';
const DEFAULT_LOGIN = { email: 'admin@example.com', password: 'password', device_name: 'web' };

const navItems = [
  { label: 'Overview', path: '/' },
  { label: 'Employees', path: '/employees' },
  { label: 'Users', path: '/users' },
  { label: 'Departments', path: '/departments' },
  { label: 'Positions', path: '/positions' },
  { label: 'Roles', path: '/roles' },
  { label: 'Attendance', path: '/attendance' },
  { label: 'Leaves', path: '/leaves' },
  { label: 'Holidays', path: '/holidays' },
  { label: 'Leave balances', path: '/leave-balances' },
  { label: 'Payroll', path: '/payroll' },
  { label: 'Documents', path: '/documents' },
  { label: 'Settings', path: '/settings' },
];

const translations = {
  ar: {
    nav: {
      overview: 'نظرة عامة',
      employees: 'الموظفون',
      users: 'المستخدمون',
      departments: 'الأقسام',
      positions: 'المسميات الوظيفية',
      roles: 'الأدوار والصلاحيات',
      attendance: 'الحضور',
      leaves: 'الإجازات',
      holidays: 'العطل الرسمية',
      leaveBalances: 'أرصدة الإجازات',
      payroll: 'الرواتب',
      documents: 'المستندات',
      currencyCode: 'رمز العملة',
      settings: 'الإعدادات',
    },
    common: {
      loading: 'جارٍ تحميل نظام الموارد البشرية...',
      portal: 'بوابة المؤسسة',
      signIn: 'تسجيل الدخول',
      email: 'البريد الإلكتروني',
      password: 'كلمة المرور',
      login: 'دخول',
      logout: 'تسجيل الخروج',
      export: 'تصدير التقرير',
      newEmployee: 'موظف جديد',
      goodMorning: 'صباح الخير،',
      thisMonth: 'هذا الشهر',
      efficiency: 'كفاءة القوى العاملة',
      active: 'نشط',
      remote: 'عن بعد',
      newThisMonth: 'جديد هذا الشهر',
      directory: 'الدليل',
      invite: '+ دعوة موظف',
      operations: 'العمليات',
      markAttendance: '+ تسجيل الحضور',
      present: 'حاضر',
      late: 'متأخر',
      absence: 'غياب',
      people: 'الموظفون',
      leaveRequests: 'طلبات الإجازة',
      newRequest: '+ طلب جديد',
      finance: 'المالية',
      runPayroll: 'تجهيز الرواتب',
      compliance: 'الالتزام',
      documents: 'المستندات',
      uploadFile: 'رفع ملف',
      configuration: 'الإعدادات',
      save: 'حفظ التغييرات',
      companyName: 'اسم الشركة',
      timezone: 'المنطقة الزمنية',
      workWeek: 'أيام العمل',
      leaveDays: 'أيام الإجازة السنوية',
      performance: 'الأداء',
      productivity: 'إنتاجية الفريق',
      thisYear: 'هذه السنة',
      hiring: 'التوظيف',
      pipeline: 'مسار التوظيف',
      filled: 'مُعبأ',
      open: 'مفتوح',
      offer: 'عرض',
      hired: 'تم التعيين',
      quickActions: 'إجراءات سريعة',
      tools: 'الأدوات',
      topPerformers: 'أفضل الأداء',
      viewAll: 'عرض الكل',
      today: 'اليوم',
      agenda: 'جدول النشاط',
      pulse: 'النشاط',
      departmentHealth: 'صحة الأقسام',
      empty: 'لا توجد بيانات',
    },
  },
  en: {
    nav: {
      overview: 'Overview',
      employees: 'Employees',
      users: 'Users',
      departments: 'Departments',
      positions: 'Positions',
      roles: 'Roles & permissions',
      attendance: 'Attendance',
      leaves: 'Leaves',
      holidays: 'Holidays',
      leaveBalances: 'Leave balances',
      payroll: 'Payroll',
      documents: 'Documents',
      currencyCode: 'Currency code',
      settings: 'Settings',
    },
    common: {
      loading: 'Loading HR Suite...',
      portal: 'Enterprise Portal',
      signIn: 'Sign in',
      email: 'Email',
      password: 'Password',
      login: 'Login',
      logout: 'Sign out',
      export: 'Export report',
      newEmployee: '+ New employee',
      goodMorning: 'Good morning,',
      thisMonth: 'This month',
      efficiency: 'Workforce efficiency',
      active: 'Active',
      remote: 'Remote',
      newThisMonth: 'New this month',
      directory: 'Directory',
      invite: '+ Invite employee',
      operations: 'Operations',
      markAttendance: '+ Mark attendance',
      present: 'Present',
      late: 'Late',
      absence: 'Absence',
      people: 'People',
      leaveRequests: 'Leave requests',
      newRequest: '+ New request',
      finance: 'Finance',
      runPayroll: 'Run payroll',
      compliance: 'Compliance',
      documents: 'Documents',
      uploadFile: 'Upload file',
      configuration: 'Configuration',
      save: 'Save changes',
      companyName: 'Company name',
      timezone: 'Timezone',
      workWeek: 'Work week',
      leaveDays: 'Annual leave days',
      performance: 'Performance',
      productivity: 'Team productivity',
      thisYear: 'This year',
      hiring: 'Hiring',
      pipeline: 'Recruitment pipeline',
      filled: 'Filled',
      open: 'Open',
      offer: 'Offer',
      hired: 'Hired',
      quickActions: 'Fast actions',
      tools: 'Quick tools',
      topPerformers: 'Top performers',
      viewAll: 'View all',
      today: 'Today',
      agenda: 'Agenda',
      pulse: 'Pulse',
      departmentHealth: 'Department health',
      empty: 'No data available',
    },
  },
};

const formatCurrency = (value, currencyCode = 'SAR', locale = 'en-US') => {
  try {
    return new Intl.NumberFormat(locale, { style: 'currency', currency: currencyCode, maximumFractionDigits: 2 }).format(value ?? 0);
  } catch {
    return `${new Intl.NumberFormat(locale).format(value ?? 0)} ${currencyCode}`;
  }
};

const getAuthHeaders = () => ({
  Accept: 'application/json',
  Authorization: `Bearer ${localStorage.getItem('hr_token')}`,
});

const quickActions = ['Approve leave requests', 'Review time sheets', 'Plan onboarding', 'Update policies'];

const fetchJson = async (url, options = {}) => {
  const response = await fetch(url, options);
  const contentType = response.headers.get('content-type') || '';
  const data = contentType.includes('application/json') ? await response.json() : await response.text();

  return { response, data };
};

const normalizeCollection = (value) => {
  if (Array.isArray(value)) {
    return value;
  }

  if (value && Array.isArray(value.data)) {
    return value.data;
  }

  return [];
};

export default function App() {
  const [lang, setLang] = useState(() => localStorage.getItem('hr_lang') || 'ar');
  const [pathname, setPathname] = useState(window.location.pathname);
  const [auth, setAuth] = useState(null);
  const [employees, setEmployees] = useState([]);
  const [users, setUsers] = useState([]);
  const [roles, setRoles] = useState([]);
  const [permissions, setPermissions] = useState([]);
  const [departments, setDepartments] = useState([]);
  const [positions, setPositions] = useState([]);
  const [attendance, setAttendance] = useState([]);
  const [holidays, setHolidays] = useState([]);
  const [leaveBalances, setLeaveBalances] = useState([]);
  const [salaries, setSalaries] = useState([]);
  const [organization, setOrganization] = useState({ company_name: 'اسم المؤسسة', currency_code: 'SAR' });
  const [loading, setLoading] = useState(true);
  const [authError, setAuthError] = useState('');
  const [loginForm, setLoginForm] = useState(DEFAULT_LOGIN);
  const [leaves, setLeaves] = useState([]);
  const [documents, setDocuments] = useState([]);
  const [actionError, setActionError] = useState('');
  const [saving, setSaving] = useState(false);
  const [employeeQuery, setEmployeeQuery] = useState('');
  const [documentForm, setDocumentForm] = useState({ document_name: '', document_type: 'contract', employee_id: '', expiry_date: '', notes: '', file: null });
  const [salaryForm, setSalaryForm] = useState({ employee_id: '', year: String(new Date().getFullYear()), month: String(new Date().getMonth() + 1), basic_amount: '', deductions: '0', bonus: '0', overtime_amount: '0' });
  const [organizationSaving, setOrganizationSaving] = useState(false);
  const [editingEntityId, setEditingEntityId] = useState(null);
  const [entityForm, setEntityForm] = useState({ name: '', code: '', description: '', location: '', parent_department_id: '', is_active: true });
  const [editingRoleId, setEditingRoleId] = useState(null);
  const [roleForm, setRoleForm] = useState({ role_name: '', display_name: '', description: '', is_active: true, permission_ids: [] });
  const [activeModal, setActiveModal] = useState(null);
  const [newEmployeeForm, setNewEmployeeForm] = useState({
    user_id: '',
    department_id: '',
    position_id: '',
    employee_code: '',
    name: '',
    email: '',
    phone: '',
    basic_salary: '',
    hire_date: '',
    birth_date: '',
    address: '',
    gender: '',
    status: 'active',
    work_shift: 'day',
  });
  const [attendanceForm, setAttendanceForm] = useState({
    employee_id: '',
    date: new Date().toISOString().slice(0, 10),
    shift: 'day',
    status: 'present',
  });
  const [leaveForm, setLeaveForm] = useState({
    employee_id: '',
    leave_type: 'earned',
    start_date: new Date().toISOString().slice(0, 10),
    end_date: new Date().toISOString().slice(0, 10),
    reason: '',
  });
  const [editingEmployeeId, setEditingEmployeeId] = useState(null);
  const [editingAttendanceId, setEditingAttendanceId] = useState(null);
  const [editingLeaveId, setEditingLeaveId] = useState(null);
  const [editingHolidayId, setEditingHolidayId] = useState(null);
  const [editingLeaveBalanceId, setEditingLeaveBalanceId] = useState(null);
  const [holidayForm, setHolidayForm] = useState({ holiday_name: '', holiday_date: '', description: '', is_recurring: false });
  const [leaveBalanceForm, setLeaveBalanceForm] = useState({ employee_id: '', year: String(new Date().getFullYear()), leave_type: 'earned', allocated_days: '' });
  const [userForm, setUserForm] = useState({ name: '', email: '', password: '', password_confirmation: '', is_active: true, role_ids: [] });
  const [editingUserId, setEditingUserId] = useState(null);

  const apiRequest = async (url, options = {}) => {
    const { response, data } = await fetchJson(url, {
      ...options,
      headers: { ...getAuthHeaders(), ...(options.headers || {}) },
    });

    if (!response.ok) {
      const validationMessage = Object.values(data?.errors || {}).flat()[0];
      throw new Error(validationMessage || data?.message || (lang === 'ar' ? 'تعذر إتمام العملية.' : 'The operation could not be completed.'));
    }

    return data;
  };

  const resetEmployeeForm = () => {
    setNewEmployeeForm({
      user_id: '',
      department_id: departments[0]?.id ? String(departments[0].id) : '',
      position_id: positions[0]?.id ? String(positions[0].id) : '',
      employee_code: '',
      name: '',
      email: '',
      phone: '',
      basic_salary: '',
      hire_date: '',
      birth_date: '',
      address: '',
      gender: '',
      status: 'active',
      work_shift: 'day',
    });
  };

  const resetAttendanceForm = () => {
    setAttendanceForm({
      employee_id: employees[0]?.id ? String(employees[0].id) : '',
      date: new Date().toISOString().slice(0, 10),
      shift: employees[0]?.work_shift || 'day',
      status: 'present',
    });
  };

  const resetLeaveForm = () => {
    setLeaveForm({
      employee_id: employees[0]?.id ? String(employees[0].id) : '',
      leave_type: 'earned',
      start_date: new Date().toISOString().slice(0, 10),
      end_date: new Date().toISOString().slice(0, 10),
      reason: '',
    });
  };

  const t = translations[lang] || translations.ar;
  const formatAmount = (value) => formatCurrency(value, organization.currency_code || 'SAR', lang === 'ar' ? 'ar-SA' : 'en-US');

  useEffect(() => {
    localStorage.setItem('hr_lang', lang);
  }, [lang]);

  const stats = useMemo(
    () => ({
      totalEmployees: employees.length,
      attendanceRate: attendance.length
        ? Math.round((attendance.filter((item) => item.status === 'present' || item.status === 'on_time').length / attendance.length) * 100)
        : 0,
      payroll: salaries.reduce((sum, salary) => sum + Number(salary.net_amount || salary.basic_amount || 0), 0),
      pendingApprovals: leaves.filter((leave) => leave.status === 'pending').length,
    }),
    [employees, attendance, leaves, salaries],
  );

  const statCards = useMemo(
    () => [
      { label: lang === 'ar' ? 'إجمالي الموظفين' : 'Total employees', value: stats.totalEmployees.toLocaleString(), change: lang === 'ar' ? 'مباشر' : 'Live', tone: 'emerald' },
      { label: lang === 'ar' ? 'طلبات الانتظار' : 'Pending approvals', value: String(stats.pendingApprovals), change: lang === 'ar' ? 'مباشر' : 'Live', tone: 'rose' },
      { label: lang === 'ar' ? 'معدل الحضور' : 'Attendance rate', value: `${stats.attendanceRate}%`, change: lang === 'ar' ? 'مباشر' : 'Live', tone: 'sky' },
      { label: lang === 'ar' ? 'إجمالي الرواتب' : 'Payroll total', value: formatAmount(stats.payroll), change: lang === 'ar' ? 'مباشر' : 'Live', tone: 'violet' },
    ],
    [stats, lang, organization.currency_code],
  );

  useEffect(() => {
    const push = () => setPathname(window.location.pathname);
    window.addEventListener('popstate', push);
    return () => window.removeEventListener('popstate', push);
  }, []);

  useEffect(() => {
    let isCurrent = true;
    const token = localStorage.getItem('hr_token');
    const redirectToLogin = () => {
      window.history.replaceState({}, '', '/login');
      setPathname('/login');
    };

    if (!token) {
      redirectToLogin();
      setLoading(false);
      return () => {
        isCurrent = false;
      };
    }

    const restoreSession = async () => {
      try {
        const { response, data } = await fetchJson(`${API_BASE}/auth/me`, { headers: getAuthHeaders() });

        if (!response.ok) {
          throw new Error(data?.message || 'Session expired');
        }

        const user = data?.user || data;

        if (isCurrent) {
          setAuth(user);
          if (window.location.pathname === '/login') {
            window.history.replaceState({}, '', '/');
            setPathname('/');
          }
        }
      } catch {
        localStorage.removeItem('hr_token');
        if (isCurrent) {
          setAuth(null);
          redirectToLogin();
        }
      } finally {
        if (isCurrent) {
          setLoading(false);
        }
      }
    };

    restoreSession();

    return () => {
      isCurrent = false;
    };
  }, []);

  useEffect(() => {
    if (!auth) {
      return undefined;
    }

    let isCurrent = true;
    const load = async () => {
      setLoading(true);
      try {
        const requests = [
          fetchJson(`${API_BASE}/employees?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/attendances?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/leaves?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/holidays?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/leave-balances?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/documents?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/users?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/roles?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/permissions?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/departments?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/positions?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/salaries?per_page=100`, { headers: getAuthHeaders() }),
          fetchJson(`${API_BASE}/organization`, { headers: getAuthHeaders() }),
        ];

        const [employeesRes, attendanceRes, leavesRes, holidaysRes, leaveBalancesRes, documentsRes, usersRes, rolesRes, permissionsRes, departmentsRes, positionsRes, salariesRes, organizationRes] = await Promise.all(requests);

        if ([employeesRes.response, attendanceRes.response, leavesRes.response, holidaysRes.response, leaveBalancesRes.response, documentsRes.response].some((response) => response.status === 401)) {
          localStorage.removeItem('hr_token');
          setAuth(null);
          window.history.replaceState({}, '', '/login');
          setPathname('/login');
          return;
        }

        if (isCurrent) {
          setEmployees(normalizeCollection(employeesRes.data));
          setAttendance(normalizeCollection(attendanceRes.data));
          setLeaves(normalizeCollection(leavesRes.data));
          setHolidays(normalizeCollection(holidaysRes.data));
          setLeaveBalances(normalizeCollection(leaveBalancesRes.data));
          setDocuments(normalizeCollection(documentsRes.data));
          setUsers(normalizeCollection(usersRes.data));
          setRoles(normalizeCollection(rolesRes.data));
          setPermissions(normalizeCollection(permissionsRes.data));
          setDepartments(normalizeCollection(departmentsRes.data));
          setPositions(normalizeCollection(positionsRes.data));
          setSalaries(normalizeCollection(salariesRes.data));
          if (organizationRes.response.ok) {
            setOrganization(organizationRes.data?.data || organizationRes.data || { company_name: 'اسم المؤسسة' });
          }
        }
      } catch (error) {
        console.error('Failed to load HR data', error);
      } finally {
        if (isCurrent) {
          setLoading(false);
        }
      }
    };

    load();

    return () => {
      isCurrent = false;
    };
  }, [auth]);

  const login = async (event) => {
    event.preventDefault();
    setAuthError('');

    try {
      const { response, data } = await fetchJson(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify(loginForm),
      });

      if (!response.ok) {
        throw new Error(data?.message || 'Login failed');
      }

      localStorage.setItem('hr_token', data.token);
      setAuth(data.user || data);
      setLoginForm(DEFAULT_LOGIN);
      window.history.pushState({}, '', '/');
      setPathname('/');
    } catch (error) {
      setAuthError(error.message || 'Login failed');
    }
  };

  const logout = async () => {
    try {
      await fetch(`${API_BASE}/auth/logout`, {
        method: 'POST',
        headers: getAuthHeaders(),
      });
    } catch (error) {
      console.error('Failed to revoke API token', error);
    } finally {
      localStorage.removeItem('hr_token');
      setAuth(null);
      setEmployees([]);
      setUsers([]);
      setRoles([]);
      setDepartments([]);
      setPositions([]);
      setAttendance([]);
      setHolidays([]);
      setLeaveBalances([]);
      setSalaries([]);
      setLeaves([]);
      setDocuments([]);
      window.history.replaceState({}, '', '/login');
      setPathname('/login');
    }
  };

  const handleSaveEmployee = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    const payload = {
      ...newEmployeeForm,
      user_id: Number(newEmployeeForm.user_id),
      department_id: Number(newEmployeeForm.department_id),
      position_id: Number(newEmployeeForm.position_id),
      basic_salary: newEmployeeForm.basic_salary === '' ? null : Number(newEmployeeForm.basic_salary),
      phone: newEmployeeForm.phone || null,
      hire_date: newEmployeeForm.hire_date || null,
      birth_date: newEmployeeForm.birth_date || null,
      address: newEmployeeForm.address || null,
      gender: newEmployeeForm.gender || null,
    };

    try {
      const data = await apiRequest(editingEmployeeId ? `${API_BASE}/employees/${editingEmployeeId}` : `${API_BASE}/employees`, {
        method: editingEmployeeId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const savedEmployee = data?.data || data;
      setEmployees((current) => editingEmployeeId
        ? current.map((item) => item.id === editingEmployeeId ? { ...item, ...savedEmployee } : item)
        : [savedEmployee, ...current]);
      setEditingEmployeeId(null);
      resetEmployeeForm();
      setActiveModal(null);
      setPathname('/employees');
      window.history.pushState({}, '', '/employees');
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const openEmployeeModal = (employee = null) => {
    if (employee) {
      setEditingEmployeeId(employee.id ?? null);
      setNewEmployeeForm({
        user_id: String(employee.user_id || ''),
        department_id: String(employee.department_id || employee.department?.id || ''),
        position_id: String(employee.position_id || employee.position?.id || ''),
        employee_code: employee.employee_code || '',
        name: employee.name || '',
        email: employee.email || '',
        phone: employee.phone || '',
        basic_salary: employee.basic_salary ?? '',
        hire_date: employee.hire_date || '',
        birth_date: employee.birth_date || '',
        address: employee.address || '',
        gender: employee.gender || '',
        status: employee.status || 'active',
        work_shift: employee.work_shift || 'day',
      });
    } else {
      setEditingEmployeeId(null);
      resetEmployeeForm();
    }

    setActiveModal('employee');
  };

  const handleDeleteEmployee = async (id) => {
    if (!window.confirm(lang === 'ar' ? 'هل تريد حذف سجل الموظف؟' : 'Delete this employee record?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/employees/${id}`, { method: 'DELETE' });
      setEmployees((current) => current.filter((employee) => employee.id !== id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const openUserModal = (user = null) => {
    setActionError('');
    if (user) {
      setEditingUserId(user.id);
      setUserForm({
        name: user.name || '',
        email: user.email || '',
        password: '',
        password_confirmation: '',
        is_active: Boolean(user.is_active),
        role_ids: (user.roles || []).map((role) => String(role.id)),
      });
    } else {
      setEditingUserId(null);
      setUserForm({ name: '', email: '', password: '', password_confirmation: '', is_active: true, role_ids: [] });
    }

    setActiveModal('user');
  };

  const handleSaveUser = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);
    const payload = { ...userForm, role_ids: userForm.role_ids.map(Number) };
    if (editingUserId && !payload.password) {
      delete payload.password;
      delete payload.password_confirmation;
    }

    try {
      const data = await apiRequest(editingUserId ? `${API_BASE}/users/${editingUserId}` : `${API_BASE}/users`, {
        method: editingUserId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const savedUser = data?.data || data;
      setUsers((current) => editingUserId
        ? current.map((user) => user.id === editingUserId ? savedUser : user)
        : [savedUser, ...current]);
      setEditingUserId(null);
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleToggleUser = async (user) => {
    try {
      const data = await apiRequest(`${API_BASE}/users/${user.id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ is_active: !user.is_active }),
      });
      const updatedUser = data?.data || data;
      setUsers((current) => current.map((item) => item.id === user.id ? updatedUser : item));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const handleSaveOrganization = async (event) => {
    event.preventDefault();
    setActionError('');
    setOrganizationSaving(true);

    try {
      const normalizeTime = (value, fallback) => {
        const time = String(value || fallback);
        return time.length === 5 ? `${time}:00` : time.slice(0, 8);
      };
      const payload = {
        ...organization,
        company_name: organization.company_name || organization.name,
        currency_code: organization.currency_code || 'SAR',
        work_start_time: normalizeTime(organization.work_start_time, '08:00:00'),
        work_end_time: normalizeTime(organization.work_end_time, '17:00:00'),
        day_shift_start_time: normalizeTime(organization.day_shift_start_time, '08:00:00'),
        day_shift_end_time: normalizeTime(organization.day_shift_end_time, '17:00:00'),
        night_shift_start_time: normalizeTime(organization.night_shift_start_time, '20:00:00'),
        night_shift_end_time: normalizeTime(organization.night_shift_end_time, '05:00:00'),
        weekend_days: organization.weekend_days || ['Friday', 'Saturday'],
        annual_leave_days: Number(organization.annual_leave_days || 22),
      };
      const data = await apiRequest(`${API_BASE}/organization`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      setOrganization(data?.data || data);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setOrganizationSaving(false);
    }
  };

  const handleSaveDocument = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);
    const formData = new FormData();
    Object.entries(documentForm).forEach(([key, value]) => {
      if (value !== null && value !== '') {
        formData.append(key, value);
      }
    });

    try {
      const data = await apiRequest(`${API_BASE}/documents`, { method: 'POST', body: formData });
      const document = data?.data || data;
      setDocuments((current) => [document, ...current]);
      setDocumentForm({ document_name: '', document_type: 'contract', employee_id: '', expiry_date: '', notes: '', file: null });
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteDocument = async (id) => {
    if (!window.confirm(lang === 'ar' ? 'هل تريد حذف المستند؟' : 'Delete this document?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/documents/${id}`, { method: 'DELETE' });
      setDocuments((current) => current.filter((document) => document.id !== id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const handleSaveSalary = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    try {
      const data = await apiRequest(`${API_BASE}/salaries`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...salaryForm,
          employee_id: Number(salaryForm.employee_id),
          year: Number(salaryForm.year),
          month: Number(salaryForm.month),
          basic_amount: Number(salaryForm.basic_amount),
          deductions: Number(salaryForm.deductions || 0),
          bonus: Number(salaryForm.bonus || 0),
          overtime_amount: Number(salaryForm.overtime_amount || 0),
        }),
      });
      setSalaries((current) => [data?.data || data, ...current]);
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleSalaryDecision = async (salary, decision) => {
    try {
      const data = await apiRequest(`${API_BASE}/salaries/${salary.id}/decision`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ decision }),
      });
      const updatedSalary = data?.data || data;
      setSalaries((current) => current.map((item) => item.id === salary.id ? { ...item, ...updatedSalary } : item));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const handlePaySalary = async (salary) => {
    try {
      const data = await apiRequest(`${API_BASE}/salaries/${salary.id}/pay`, { method: 'POST' });
      const updatedSalary = data?.data || data;
      setSalaries((current) => current.map((item) => item.id === salary.id ? { ...item, ...updatedSalary } : item));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const openEntityModal = (kind, entity = null) => {
    setActionError('');
    setEditingEntityId(entity?.id ?? null);
    setEntityForm(entity ? {
      name: entity.department_name || entity.position_name || entity.name || '',
      code: entity.code || '',
      description: entity.description || '',
      location: entity.location || '',
      parent_department_id: entity.parent_department_id ? String(entity.parent_department_id) : '',
      is_active: entity.is_active !== false,
    } : { name: '', code: '', description: '', location: '', parent_department_id: '', is_active: true });
    setActiveModal(kind);
  };

  const handleSaveEntity = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);
    const isDepartment = activeModal === 'department';
    const payload = isDepartment
      ? { department_name: entityForm.name, code: entityForm.code, description: entityForm.description || null, location: entityForm.location || null, parent_department_id: entityForm.parent_department_id ? Number(entityForm.parent_department_id) : null, is_active: entityForm.is_active }
      : { position_name: entityForm.name, code: entityForm.code, description: entityForm.description || null, is_active: entityForm.is_active };

    try {
      const endpoint = isDepartment ? 'departments' : 'positions';
      const data = await apiRequest(`${API_BASE}/${endpoint}${editingEntityId ? `/${editingEntityId}` : ''}`, {
        method: editingEntityId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const savedEntity = data?.data || data;
      (isDepartment ? setDepartments : setPositions)((current) => editingEntityId
        ? current.map((item) => item.id === editingEntityId ? savedEntity : item)
        : [savedEntity, ...current]);
      setEditingEntityId(null);
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteEntity = async (kind, entity) => {
    if (!window.confirm(lang === 'ar' ? 'هل تريد حذف هذا السجل؟' : 'Delete this record?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/${kind === 'department' ? 'departments' : 'positions'}/${entity.id}`, { method: 'DELETE' });
      (kind === 'department' ? setDepartments : setPositions)((current) => current.filter((item) => item.id !== entity.id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const openRoleModal = (role = null) => {
    setActionError('');
    setEditingRoleId(role?.id ?? null);
    setRoleForm(role ? {
      role_name: role.role_name || '',
      display_name: role.display_name || '',
      description: role.description || '',
      is_active: role.is_active !== false,
      permission_ids: (role.permissions || []).map((permission) => String(permission.id)),
    } : { role_name: '', display_name: '', description: '', is_active: true, permission_ids: [] });
    setActiveModal('role');
  };

  const handleSaveRole = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    try {
      const data = await apiRequest(`${API_BASE}/roles${editingRoleId ? `/${editingRoleId}` : ''}`, {
        method: editingRoleId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...roleForm, permission_ids: roleForm.permission_ids.map(Number) }),
      });
      const savedRole = data?.data || data;
      setRoles((current) => editingRoleId
        ? current.map((role) => role.id === editingRoleId ? savedRole : role)
        : [savedRole, ...current]);
      setEditingRoleId(null);
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteRole = async (role) => {
    if (!window.confirm(lang === 'ar' ? 'حذف الدور سيؤثر على المستخدمين المرتبطين به. متابعة؟' : 'Deleting this role affects assigned users. Continue?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/roles/${role.id}`, { method: 'DELETE' });
      setRoles((current) => current.filter((item) => item.id !== role.id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const handleExport = () => {
    const exportData = pathname === '/employees'
      ? employees.map((employee) => ({ employee_code: employee.employee_code, name: employee.name, email: employee.email, phone: employee.phone, department: employee.department?.name, position: employee.position?.name, status: employee.status }))
      : pathname === '/attendance'
        ? attendance.map((item) => ({ employee: item.employee?.name || employees.find((employee) => employee.id === item.employee_id)?.name, date: item.attendance_date, shift: item.shift, status: item.status, notes: item.notes }))
        : pathname === '/leaves'
          ? leaves.map((item) => ({ employee: item.employee?.name || employees.find((employee) => employee.id === item.employee_id)?.name, leave_type: item.leave_type, start_date: item.start_date, end_date: item.end_date, status: item.status, reason: item.reason }))
          : pathname === '/holidays'
            ? holidays.map((item) => ({ holiday_name: item.holiday_name, holiday_date: item.holiday_date, is_recurring: item.is_recurring, description: item.description }))
            : pathname === '/leave-balances'
              ? leaveBalances.map((item) => ({ employee: item.employee?.name || employees.find((employee) => employee.id === item.employee_id)?.name, year: item.year, leave_type: item.leave_type, allocated_days: item.allocated_days, used_days: item.used_days }))
              : salaries.map((item) => ({ employee: item.employee?.name || employees.find((employee) => employee.id === item.employee_id)?.name, year: item.year, month: item.month, basic_amount: item.basic_amount, deductions: item.deductions, net_amount: item.net_amount, status: item.status }));
    const columns = [...new Set(exportData.flatMap((item) => Object.keys(item)))];
    const escapeCell = (value) => `"${String(value ?? '').replace(/"/g, '""')}"`;
    const csv = [columns, ...exportData.map((item) => columns.map((column) => item[column] ?? ''))]
      .map((row) => row.map(escapeCell).join(','))
      .join('\r\n');
    const file = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${pathname.slice(1) || 'hr-report'}-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(url);
  };

  const handleSaveAttendance = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    try {
      const data = await apiRequest(editingAttendanceId ? `${API_BASE}/attendances/${editingAttendanceId}` : `${API_BASE}/attendances`, {
        method: editingAttendanceId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          employee_id: Number(attendanceForm.employee_id),
          attendance_date: attendanceForm.date,
          shift: attendanceForm.shift,
          status: attendanceForm.status,
        }),
      });
      const savedRecord = data?.data || data;
      setAttendance((current) => editingAttendanceId
        ? current.map((item) => item.id === editingAttendanceId ? savedRecord : item)
        : [savedRecord, ...current]);
      setEditingAttendanceId(null);
      resetAttendanceForm();
      setActiveModal(null);
      setPathname('/attendance');
      window.history.pushState({}, '', '/attendance');
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const openAttendanceModal = (item = null) => {
    if (item) {
      setEditingAttendanceId(item.id ?? null);
      setAttendanceForm({
        employee_id: String(item.employee_id || item.employee?.id || ''),
        date: item.attendance_date || (item.check_in_at ? String(item.check_in_at).slice(0, 10) : new Date().toISOString().slice(0, 10)),
        shift: item.shift || 'day',
        status: item.status || 'present',
      });
    } else {
      setEditingAttendanceId(null);
      resetAttendanceForm();
    }

    setActiveModal('attendance');
  };

  const handleSaveLeave = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    try {
      const data = await apiRequest(editingLeaveId ? `${API_BASE}/leaves/${editingLeaveId}` : `${API_BASE}/leaves`, {
        method: editingLeaveId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          employee_id: Number(leaveForm.employee_id),
          leave_type: leaveForm.leave_type,
          start_date: leaveForm.start_date,
          end_date: leaveForm.end_date,
          reason: leaveForm.reason || null,
        }),
      });
      const savedLeave = data?.data || data;
      setLeaves((current) => editingLeaveId
        ? current.map((item) => item.id === editingLeaveId ? savedLeave : item)
        : [savedLeave, ...current]);
      setEditingLeaveId(null);
      resetLeaveForm();
      setActiveModal(null);
      setPathname('/leaves');
      window.history.pushState({}, '', '/leaves');
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const openLeaveModal = (item = null) => {
    if (item) {
      setEditingLeaveId(item.id ?? null);
      setLeaveForm({
        employee_id: String(item.employee_id || item.employee?.id || ''),
        leave_type: item.leave_type || 'earned',
        start_date: item.start_date || new Date().toISOString().slice(0, 10),
        end_date: item.end_date || item.start_date || new Date().toISOString().slice(0, 10),
        reason: item.reason || '',
      });
    } else {
      setEditingLeaveId(null);
      resetLeaveForm();
    }

    setActiveModal('leave');
  };

  const handleDeleteLeave = async (id) => {
    if (!window.confirm(lang === 'ar' ? 'هل تريد حذف طلب الإجازة؟' : 'Delete this leave request?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/leaves/${id}`, { method: 'DELETE' });
      setLeaves((current) => current.filter((item) => item.id !== id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const openHolidayModal = (holiday = null) => {
    setActionError('');
    setEditingHolidayId(holiday?.id ?? null);
    setHolidayForm(holiday ? {
      holiday_name: holiday.holiday_name || '',
      holiday_date: String(holiday.holiday_date || '').slice(0, 10),
      description: holiday.description || '',
      is_recurring: Boolean(holiday.is_recurring),
    } : { holiday_name: '', holiday_date: '', description: '', is_recurring: false });
    setActiveModal('holiday');
  };

  const handleSaveHoliday = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    try {
      const data = await apiRequest(`${API_BASE}/holidays${editingHolidayId ? `/${editingHolidayId}` : ''}`, {
        method: editingHolidayId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(holidayForm),
      });
      const savedHoliday = data?.data || data;
      setHolidays((current) => editingHolidayId
        ? current.map((item) => item.id === editingHolidayId ? savedHoliday : item)
        : [savedHoliday, ...current]);
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteHoliday = async (holiday) => {
    if (!window.confirm(lang === 'ar' ? 'هل تريد حذف هذه العطلة؟' : 'Delete this holiday?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/holidays/${holiday.id}`, { method: 'DELETE' });
      setHolidays((current) => current.filter((item) => item.id !== holiday.id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const openLeaveBalanceModal = (balance = null) => {
    setActionError('');
    setEditingLeaveBalanceId(balance?.id ?? null);
    setLeaveBalanceForm(balance ? {
      employee_id: String(balance.employee_id || balance.employee?.id || ''),
      year: String(balance.year || new Date().getFullYear()),
      leave_type: balance.leave_type || 'earned',
      allocated_days: String(balance.allocated_days ?? ''),
    } : {
      employee_id: employees[0]?.id ? String(employees[0].id) : '',
      year: String(new Date().getFullYear()),
      leave_type: 'earned',
      allocated_days: '',
    });
    setActiveModal('leaveBalance');
  };

  const handleSaveLeaveBalance = async (event) => {
    event.preventDefault();
    setActionError('');
    setSaving(true);

    try {
      const payload = {
        ...leaveBalanceForm,
        employee_id: Number(leaveBalanceForm.employee_id),
        year: Number(leaveBalanceForm.year),
        allocated_days: Number(leaveBalanceForm.allocated_days),
      };
      const data = await apiRequest(`${API_BASE}/leave-balances${editingLeaveBalanceId ? `/${editingLeaveBalanceId}` : ''}`, {
        method: editingLeaveBalanceId ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const savedBalance = data?.data || data;
      setLeaveBalances((current) => editingLeaveBalanceId
        ? current.map((item) => item.id === editingLeaveBalanceId ? savedBalance : item)
        : [savedBalance, ...current]);
      setActiveModal(null);
    } catch (error) {
      setActionError(error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteLeaveBalance = async (balance) => {
    if (!window.confirm(lang === 'ar' ? 'هل تريد حذف رصيد الإجازة؟' : 'Delete this leave balance?')) {
      return;
    }

    try {
      await apiRequest(`${API_BASE}/leave-balances/${balance.id}`, { method: 'DELETE' });
      setLeaveBalances((current) => current.filter((item) => item.id !== balance.id));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const handleLeaveDecision = async (leave, decision) => {
    const rejectionReason = decision === 'rejected'
      ? window.prompt(lang === 'ar' ? 'اكتب سبب رفض الطلب:' : 'Enter a rejection reason:')
      : null;

    if (decision === 'rejected' && !rejectionReason?.trim()) {
      return;
    }

    try {
      const data = await apiRequest(`${API_BASE}/leaves/${leave.id}/decision`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ decision, ...(rejectionReason ? { rejection_reason: rejectionReason } : {}) }),
      });
      const updatedLeave = data?.data || data;
      setLeaves((current) => current.map((item) => item.id === leave.id ? { ...item, ...updatedLeave } : item));
    } catch (error) {
      setActionError(error.message);
    }
  };

  const getCurrentTitle = () => {
    const map = {
      '/': t.nav.overview,
      '/employees': t.nav.employees,
      '/users': t.nav.users,
      '/departments': t.nav.departments,
      '/positions': t.nav.positions,
      '/roles': t.nav.roles,
      '/attendance': t.nav.attendance,
      '/leaves': t.nav.leaves,
      '/holidays': t.nav.holidays,
      '/leave-balances': t.nav.leaveBalances,
      '/payroll': t.nav.payroll,
      '/documents': t.nav.documents,
      '/settings': t.nav.settings,
    };

    return map[pathname] || t.nav.overview;
  };

  const navLabels = [
    { label: t.nav.overview, path: '/' },
    { label: t.nav.employees, path: '/employees' },
    { label: t.nav.users, path: '/users' },
    { label: t.nav.departments, path: '/departments' },
    { label: t.nav.positions, path: '/positions' },
    { label: t.nav.roles, path: '/roles' },
    { label: t.nav.attendance, path: '/attendance' },
    { label: t.nav.leaves, path: '/leaves' },
    { label: t.nav.payroll, path: '/payroll' },
    { label: t.nav.documents, path: '/documents' },
    { label: t.nav.settings, path: '/settings' },
  ];

  if (loading && pathname !== '/login') {
    return (
      <div className="grid min-h-screen place-items-center bg-slate-100 text-sm font-medium text-slate-600">
        {t.common.loading}
      </div>
    );
  }

  if (!auth && pathname === '/login') {
    return (
      <div className="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.18),_transparent_26%),linear-gradient(135deg,_#f8fafc,_#eef2ff_40%,_#e0f2fe)] p-6">
        <div className="w-full max-w-md rounded-[28px] border border-slate-200 bg-white/90 p-8 shadow-[0_25px_60px_rgba(15,23,42,0.15)] backdrop-blur-xl">
          <div className="mb-8 text-center">
            <div className="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-500 to-cyan-400 text-xl font-black text-white shadow-lg shadow-indigo-500/30">
              H
            </div>
            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.portal}</p>
            <h1 className="mt-3 text-3xl font-bold text-slate-900">{t.common.signIn}</h1>
          </div>

          <div className="mb-5 flex justify-center">
            <div className="inline-flex rounded-full border border-slate-200 bg-slate-50 p-1">
              <button
                type="button"
                onClick={() => setLang('ar')}
                className={`rounded-full px-3 py-1.5 text-sm font-semibold ${lang === 'ar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'}`}
              >
                عربي
              </button>
              <button
                type="button"
                onClick={() => setLang('en')}
                className={`rounded-full px-3 py-1.5 text-sm font-semibold ${lang === 'en' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'}`}
              >
                English
              </button>
            </div>
          </div>

          <form onSubmit={login} className="space-y-5">
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.common.email}</label>
              <input
                type="email"
                value={loginForm.email}
                onChange={(e) => setLoginForm((prev) => ({ ...prev, email: e.target.value }))}
                className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white"
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.common.password}</label>
              <input
                type="password"
                value={loginForm.password}
                onChange={(e) => setLoginForm((prev) => ({ ...prev, password: e.target.value }))}
                className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white"
              />
            </div>

            {authError && <p className="text-sm font-medium text-red-600">{authError}</p>}

            <button
              type="submit"
              className="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-violet-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:brightness-110"
            >
              {t.common.login}
            </button>
          </form>
        </div>
      </div>
    );
  }

  const renderPage = () => {
    const isArabic = lang === 'ar';
    const departmentMetrics = departments.map((department) => ({
      name: department.department_name || department.name,
      total: employees.filter((employee) => employee.department_id === department.id).length,
    }));
    const pendingLeaves = leaves.filter((leave) => leave.status === 'pending');
    const activeEmployeeCount = employees.filter((employee) => employee.status === 'active').length;
    const activeEmployeeRate = employees.length ? Math.round((activeEmployeeCount / employees.length) * 100) : 0;

    if (pathname.startsWith('/employees')) {
      return (
        <div className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
          <div className="mb-6 flex items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.directory}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.employees}</h2>
            </div>
              <button
                type="button"
                onClick={() => openEmployeeModal()}
                className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20"
              >
              {t.common.invite}
            </button>
          </div>

          <div className="mb-6 grid gap-4 md:grid-cols-3">
            {[
              { label: t.common.active, value: employees.filter((employee) => employee.status === 'active').length, tone: 'bg-emerald-50 text-emerald-700' },
              { label: isArabic ? 'ورديات ليلية' : 'Night shifts', value: employees.filter((employee) => employee.work_shift === 'night').length, tone: 'bg-sky-50 text-sky-700' },
              { label: isArabic ? 'إجمالي السجلات' : 'Total records', value: employees.length, tone: 'bg-violet-50 text-violet-700' },
            ].map((item) => (
              <div key={item.label} className="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:shadow-sm">
                <div className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${item.tone}`}>{item.label}</div>
                <div className="mt-4 text-3xl font-bold text-slate-900">{item.value}</div>
              </div>
            ))}
          </div>

          <div className="mb-4 flex items-center gap-3">
            <input value={employeeQuery} onChange={(event) => setEmployeeQuery(event.target.value)} placeholder={isArabic ? 'ابحث بالاسم أو البريد أو الرقم الوظيفي' : 'Search name, email, or employee code'} className="w-full max-w-md rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500" />
          </div>
          {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}

          <div className="overflow-hidden rounded-2xl border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-left">
              <thead className="bg-slate-50 text-xs uppercase tracking-[0.12em] text-slate-500">
                <tr>
                  <th className="px-5 py-3">{isArabic ? 'الاسم' : 'Name'}</th>
                  <th className="px-5 py-3">{isArabic ? 'القسم' : 'Department'}</th>
                  <th className="px-5 py-3">{isArabic ? 'الوظيفة' : 'Role'}</th>
                  <th className="px-5 py-3">{isArabic ? 'الحالة' : 'Status'}</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {employees.filter((employee) => `${employee.name} ${employee.email} ${employee.employee_code}`.toLowerCase().includes(employeeQuery.toLowerCase())).map((employee, index) => (
                  <tr key={employee.id ?? `${employee.name || employee.employee_name || 'Employee'}-${index}`}>
                    <td className="px-5 py-4 text-sm font-medium text-slate-800">{employee.name || employee.employee_name || 'Employee'}</td>
                    <td className="px-5 py-4 text-sm text-slate-600">{employee.department?.name ?? employee.department_name ?? employee.team ?? 'General'}</td>
                    <td className="px-5 py-4 text-sm text-slate-600">{employee.position?.name ?? employee.position_name ?? employee.role ?? 'Employee'}</td>
                    <td className="px-5 py-4">
                      <div className="flex items-center justify-between gap-3">
                        <span className={`rounded-full px-2.5 py-1 text-xs font-semibold ${employee.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'}`}>
                          {employee.status || 'Active'}
                        </span>
                        <div className="flex items-center gap-2">
                          <button
                            type="button"
                            onClick={() => openEmployeeModal(employee)}
                            className="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] font-semibold text-slate-700"
                          >
                            {isArabic ? 'تعديل' : 'Edit'}
                          </button>
                          <button
                            type="button"
                            onClick={() => handleDeleteEmployee(employee.id ?? employee.name)}
                            className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700"
                          >
                            {isArabic ? 'حذف' : 'Delete'}
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                ))}
                {!employees.length && <tr><td colSpan="4" className="px-5 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد سجلات موظفين.' : 'No employee records yet.'}</td></tr>}
              </tbody>
            </table>
          </div>
        </div>
      );
    }

    if (pathname.startsWith('/departments') || pathname.startsWith('/positions')) {
      const isDepartmentsPage = pathname.startsWith('/departments');
      const records = isDepartmentsPage ? departments : positions;
      const kind = isDepartmentsPage ? 'department' : 'position';
      const pageTitle = isDepartmentsPage ? t.nav.departments : t.nav.positions;
      return (
        <section className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
          <div className="mb-6 flex flex-wrap items-center justify-between gap-4"><div><p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'الهيكل التنظيمي' : 'Organization structure'}</p><h2 className="mt-2 text-2xl font-bold text-slate-900">{pageTitle}</h2></div><button type="button" onClick={() => openEntityModal(kind)} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">{isArabic ? 'إضافة' : 'Add'} {pageTitle}</button></div>
          {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
          <div className="overflow-x-auto rounded-2xl border border-slate-200"><table className="min-w-full divide-y divide-slate-200 text-start"><thead className="bg-slate-50 text-xs text-slate-500"><tr><th className="px-4 py-3">{isArabic ? 'الاسم' : 'Name'}</th><th className="px-4 py-3">{isArabic ? 'الرمز' : 'Code'}</th>{isDepartmentsPage && <th className="px-4 py-3">{isArabic ? 'الموقع' : 'Location'}</th>}<th className="px-4 py-3">{isArabic ? 'الحالة' : 'Status'}</th><th className="px-4 py-3">{isArabic ? 'الإجراءات' : 'Actions'}</th></tr></thead><tbody className="divide-y divide-slate-100">{records.map((item) => <tr key={item.id}><td className="px-4 py-3 text-sm font-semibold">{item.department_name || item.position_name || item.name}</td><td className="px-4 py-3 text-sm text-slate-600">{item.code}</td>{isDepartmentsPage && <td className="px-4 py-3 text-sm text-slate-600">{item.location || '-'}</td>}<td className="px-4 py-3 text-sm">{item.is_active ? (isArabic ? 'نشط' : 'Active') : (isArabic ? 'موقوف' : 'Inactive')}</td><td className="px-4 py-3"><div className="flex gap-2"><button type="button" onClick={() => openEntityModal(kind, item)} className="rounded-lg border border-slate-200 px-2 py-1 text-xs">{isArabic ? 'تعديل' : 'Edit'}</button><button type="button" onClick={() => handleDeleteEntity(kind, item)} className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs text-rose-700">{isArabic ? 'حذف' : 'Delete'}</button></div></td></tr>)}{!records.length && <tr><td colSpan={isDepartmentsPage ? 5 : 4} className="px-4 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد سجلات بعد.' : 'No records yet.'}</td></tr>}</tbody></table></div>
        </section>
      );
    }

    if (pathname.startsWith('/roles')) {
      return (
        <section className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
          <div className="mb-6 flex flex-wrap items-center justify-between gap-4"><div><p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'التحكم بالصلاحيات' : 'Access control'}</p><h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.roles}</h2></div><button type="button" onClick={() => openRoleModal()} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">{isArabic ? '+ إضافة دور' : '+ Add role'}</button></div>
          {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">{roles.map((role) => <article key={role.id} className="rounded-xl border border-slate-200 p-4"><div className="flex items-start justify-between gap-3"><div><h3 className="font-semibold text-slate-900">{role.display_name}</h3><p className="mt-1 text-xs text-slate-500">{role.role_name}</p></div><span className={`rounded-full px-2 py-1 text-[10px] font-semibold ${role.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'}`}>{role.is_active ? (isArabic ? 'نشط' : 'Active') : (isArabic ? 'متوقف' : 'Inactive')}</span></div><p className="mt-3 line-clamp-2 text-sm text-slate-600">{role.description || (isArabic ? 'لا يوجد وصف' : 'No description')}</p><div className="mt-3 text-xs text-slate-500">{(role.permissions || []).length} {isArabic ? 'صلاحية' : 'permissions'}</div><div className="mt-4 flex gap-2"><button type="button" onClick={() => openRoleModal(role)} className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs">{isArabic ? 'تعديل' : 'Edit'}</button><button type="button" onClick={() => handleDeleteRole(role)} className="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs text-rose-700">{isArabic ? 'حذف' : 'Delete'}</button></div></article>)}{!roles.length && <p className="text-sm text-slate-500">{isArabic ? 'لا توجد أدوار أو لا تملك صلاحية إدارتها.' : 'No roles available, or you do not have permission to manage them.'}</p>}</div>
        </section>
      );
    }

    if (pathname.startsWith('/users')) {
      return (
        <section className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
          <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'إدارة الوصول' : 'Access management'}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.users}</h2>
            </div>
            <button type="button" onClick={() => openUserModal()} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">
              {isArabic ? '+ إضافة مستخدم' : '+ Add user'}
            </button>
          </div>
          {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
          <div className="overflow-x-auto rounded-2xl border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-start">
              <thead className="bg-slate-50 text-xs text-slate-500">
                <tr>
                  <th className="px-4 py-3">{isArabic ? 'الاسم' : 'Name'}</th>
                  <th className="px-4 py-3">{t.common.email}</th>
                  <th className="px-4 py-3">{isArabic ? 'الأدوار' : 'Roles'}</th>
                  <th className="px-4 py-3">{isArabic ? 'حالة الحساب' : 'Account status'}</th>
                  <th className="px-4 py-3">{isArabic ? 'الإجراءات' : 'Actions'}</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {users.map((user) => (
                  <tr key={user.id}>
                    <td className="px-4 py-3 text-sm font-semibold text-slate-800">{user.name}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{user.email}</td>
                    <td className="px-4 py-3 text-sm text-slate-600">{(user.roles || []).map((role) => role.name || role.display_name).join(', ') || (isArabic ? 'بدون دور' : 'No role')}</td>
                    <td className="px-4 py-3 text-sm">{user.is_active ? (isArabic ? 'نشط' : 'Active') : (isArabic ? 'موقوف' : 'Inactive')}</td>
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-2">
                        <button type="button" onClick={() => openUserModal(user)} className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700">{isArabic ? 'تعديل' : 'Edit'}</button>
                        <button type="button" onClick={() => handleToggleUser(user)} className="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-800">{user.is_active ? (isArabic ? 'إيقاف' : 'Deactivate') : (isArabic ? 'تفعيل' : 'Activate')}</button>
                      </div>
                    </td>
                  </tr>
                ))}
                {!users.length && <tr><td colSpan="5" className="px-4 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد حسابات متاحة أو لا تملك صلاحية عرضها.' : 'No accounts available, or you do not have permission to view them.'}</td></tr>}
              </tbody>
            </table>
          </div>
        </section>
      );
    }

    if (pathname.startsWith('/attendance')) {
      return (
        <div className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
          <div className="mb-6 flex items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.operations}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.attendance}</h2>
            </div>
            <button
              type="button"
              onClick={() => openAttendanceModal()}
              className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20"
            >
              {t.common.markAttendance}
            </button>
          </div>

          <div className="mb-6 grid gap-4 md:grid-cols-3">
            {[
              { label: t.common.present, value: `${attendance.length ? Math.round((attendance.filter((item) => item.status === 'present').length / attendance.length) * 100) : 0}%`, tone: 'text-emerald-700 bg-emerald-50' },
              { label: t.common.late, value: `${attendance.length ? Math.round((attendance.filter((item) => item.status === 'late').length / attendance.length) * 100) : 0}%`, tone: 'text-amber-700 bg-amber-50' },
              { label: t.common.absence, value: `${attendance.length ? Math.round((attendance.filter((item) => item.status === 'absent').length / attendance.length) * 100) : 0}%`, tone: 'text-rose-700 bg-rose-50' },
            ].map((item) => (
              <div key={item.label} className="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:shadow-sm">
                <div className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${item.tone}`}>{item.label}</div>
                <div className="mt-4 text-3xl font-bold text-slate-900">{item.value}</div>
              </div>
            ))}
          </div>

          <div className="overflow-hidden rounded-2xl border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-left">
              <thead className="bg-slate-50 text-xs uppercase tracking-[0.12em] text-slate-500">
                <tr>
                  <th className="px-5 py-3">{isArabic ? 'الموظف' : 'Employee'}</th>
                  <th className="px-5 py-3">{isArabic ? 'الوردية' : 'Shift'}</th>
                  <th className="px-5 py-3">{isArabic ? 'وقت الدخول' : 'Check in'}</th>
                  <th className="px-5 py-3">{isArabic ? 'الحالة' : 'Status'}</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {attendance.map((item, index) => (
                  <tr key={item.id ?? `${item.employee_name || item.name || 'Employee'}-${index}`}>
                    <td className="px-5 py-4 text-sm font-medium text-slate-800">{item.employee?.name || employees.find((employee) => employee.id === item.employee_id)?.name || item.employee_name || 'Employee'}</td>
                    <td className="px-5 py-4 text-sm text-slate-600">{item.shift || item.work_shift || 'Day'}</td>
                    <td className="px-5 py-4 text-sm text-slate-600">{item.check_in_at || item.attendance_date || '-'}</td>
                    <td className="px-5 py-4">
                      <div className="flex items-center justify-between gap-3">
                        <span className={`rounded-full px-2.5 py-1 text-xs font-semibold ${item.status === 'present' ? 'bg-emerald-100 text-emerald-700' : item.status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700'}`}>
                          {item.status || 'Present'}
                        </span>
                        <div className="flex items-center gap-2">
                          <button
                            type="button"
                            onClick={() => openAttendanceModal(item)}
                            className="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] font-semibold text-slate-700"
                          >
                            {isArabic ? 'تعديل' : 'Edit'}
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                ))}
                {!attendance.length && <tr><td colSpan="4" className="px-5 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد سجلات حضور.' : 'No attendance records yet.'}</td></tr>}
              </tbody>
            </table>
          </div>
        </div>
      );
    }

    if (pathname.startsWith('/leaves')) {
      return (
        <div className="space-y-6">
          <div className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <div className="mb-6 flex items-center justify-between gap-4">
              <div>
                <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.people}</p>
                <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.common.leaveRequests}</h2>
              </div>
              <button
                type="button"
                onClick={() => openLeaveModal()}
                className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20"
              >
                {t.common.newRequest}
              </button>
            </div>

            <div className="space-y-3">
              {leaves.map((request) => {
                const employeeName = request.employee?.name || employees.find((employee) => employee.id === request.employee_id)?.name || request.employee_name || 'Employee';
                const type = request.leave_type || 'earned';
                const days = request.total_days ? `${request.total_days} day${request.total_days > 1 ? 's' : ''}` : '1 day';
                const status = request.status || 'pending';
                const tone = status.toLowerCase().includes('approve') ? 'emerald' : status.toLowerCase().includes('reject') ? 'rose' : status.toLowerCase().includes('review') ? 'violet' : 'amber';

                return (
                  <div key={request.id} className="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 md:flex-row md:items-center md:justify-between">
                    <div>
                      <div className="text-base font-semibold text-slate-900">{employeeName}</div>
                      <div className="mt-1 text-sm text-slate-500">{type} · {request.start_date} - {request.end_date} · {days}</div>
                    </div>
                    <div className="flex items-center gap-3">
                      <span className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${tone === 'emerald' ? 'bg-emerald-100 text-emerald-700' : tone === 'rose' ? 'bg-rose-100 text-rose-700' : tone === 'violet' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700'}`}>
                        {status}
                      </span>
                      <div className="flex items-center gap-2">
                        {status === 'pending' && <button type="button" onClick={() => handleLeaveDecision(request, 'approved')} className="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-800">{isArabic ? 'اعتماد' : 'Approve'}</button>}
                        {status === 'pending' && <button type="button" onClick={() => handleLeaveDecision(request, 'rejected')} className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-800">{isArabic ? 'رفض' : 'Reject'}</button>}
                        <button
                          type="button"
                          onClick={() => openLeaveModal(request)}
                          className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700"
                        >
                          {isArabic ? 'تعديل' : 'Edit'}
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDeleteLeave(request.id)}
                          className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700"
                        >
                          {isArabic ? 'حذف' : 'Delete'}
                        </button>
                      </div>
                    </div>
                  </div>
                );
              })}
              {!leaves.length && <p className="rounded-xl border border-dashed border-slate-300 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد طلبات إجازة.' : 'No leave requests yet.'}</p>}
            </div>
          </div>
        </div>
      );
    }

    if (pathname.startsWith('/holidays')) {
      return (
        <section className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
          <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'تقويم المؤسسة' : 'Company calendar'}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.holidays}</h2>
            </div>
            <button type="button" onClick={() => openHolidayModal()} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">
              {isArabic ? '+ إضافة عطلة' : '+ Add holiday'}
            </button>
          </div>
          {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
          <div className="overflow-x-auto rounded-2xl border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-start">
              <thead className="bg-slate-50 text-xs text-slate-500"><tr><th className="px-4 py-3">{isArabic ? 'اسم العطلة' : 'Holiday'}</th><th className="px-4 py-3">{isArabic ? 'التاريخ' : 'Date'}</th><th className="px-4 py-3">{isArabic ? 'التكرار السنوي' : 'Recurring'}</th><th className="px-4 py-3">{isArabic ? 'الوصف' : 'Description'}</th><th className="px-4 py-3">{isArabic ? 'الإجراءات' : 'Actions'}</th></tr></thead>
              <tbody className="divide-y divide-slate-100">
                {holidays.map((holiday) => <tr key={holiday.id}><td className="px-4 py-3 text-sm font-semibold">{holiday.holiday_name}</td><td className="px-4 py-3 text-sm text-slate-600">{String(holiday.holiday_date || '').slice(0, 10)}</td><td className="px-4 py-3 text-sm">{holiday.is_recurring ? (isArabic ? 'سنوي' : 'Yearly') : (isArabic ? 'مرة واحدة' : 'One-time')}</td><td className="px-4 py-3 text-sm text-slate-600">{holiday.description || '-'}</td><td className="px-4 py-3"><div className="flex gap-2"><button type="button" onClick={() => openHolidayModal(holiday)} className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs">{isArabic ? 'تعديل' : 'Edit'}</button><button type="button" onClick={() => handleDeleteHoliday(holiday)} className="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs text-rose-700">{isArabic ? 'حذف' : 'Delete'}</button></div></td></tr>)}
                {!holidays.length && <tr><td colSpan="5" className="px-4 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد عطل مسجلة.' : 'No holidays recorded.'}</td></tr>}
              </tbody>
            </table>
          </div>
        </section>
      );
    }

    if (pathname.startsWith('/leave-balances')) {
      const leaveTypeLabels = {
        sick: isArabic ? 'مرضية' : 'Sick',
        casual: isArabic ? 'عارضة' : 'Casual',
        earned: isArabic ? 'سنوية مستحقة' : 'Earned / annual',
        maternity: isArabic ? 'أمومة' : 'Maternity',
        paternity: isArabic ? 'أبوة' : 'Paternity',
        bereavement: isArabic ? 'وفاة' : 'Bereavement',
      };

      return (
        <section className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
          <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'إدارة الاستحقاقات' : 'Entitlements'}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.leaveBalances}</h2>
            </div>
            <button type="button" onClick={() => openLeaveBalanceModal()} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">
              {isArabic ? '+ إضافة رصيد' : '+ Add balance'}
            </button>
          </div>
          {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
          <div className="overflow-x-auto rounded-2xl border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-start">
              <thead className="bg-slate-50 text-xs text-slate-500"><tr><th className="px-4 py-3">{isArabic ? 'الموظف' : 'Employee'}</th><th className="px-4 py-3">{isArabic ? 'السنة' : 'Year'}</th><th className="px-4 py-3">{isArabic ? 'نوع الإجازة' : 'Leave type'}</th><th className="px-4 py-3">{isArabic ? 'المخصص' : 'Allocated'}</th><th className="px-4 py-3">{isArabic ? 'المستخدم' : 'Used'}</th><th className="px-4 py-3">{isArabic ? 'المتبقي' : 'Remaining'}</th><th className="px-4 py-3">{isArabic ? 'الإجراءات' : 'Actions'}</th></tr></thead>
              <tbody className="divide-y divide-slate-100">
                {leaveBalances.map((balance) => {
                  const employee = balance.employee?.name || employees.find((item) => String(item.id) === String(balance.employee_id))?.name || `#${balance.employee_id}`;
                  const allocatedDays = Number(balance.allocated_days || 0);
                  const usedDays = Number(balance.used_days || 0);

                  return <tr key={balance.id}><td className="px-4 py-3 text-sm font-semibold">{employee}</td><td className="px-4 py-3 text-sm text-slate-600">{balance.year}</td><td className="px-4 py-3 text-sm text-slate-600">{leaveTypeLabels[balance.leave_type] || balance.leave_type}</td><td className="px-4 py-3 text-sm">{allocatedDays}</td><td className="px-4 py-3 text-sm">{usedDays}</td><td className="px-4 py-3 text-sm font-semibold">{Math.max(allocatedDays - usedDays, 0)}</td><td className="px-4 py-3"><div className="flex gap-2"><button type="button" onClick={() => openLeaveBalanceModal(balance)} className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs">{isArabic ? 'تعديل' : 'Edit'}</button><button type="button" onClick={() => handleDeleteLeaveBalance(balance)} className="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs text-rose-700">{isArabic ? 'حذف' : 'Delete'}</button></div></td></tr>;
                })}
                {!leaveBalances.length && <tr><td colSpan="7" className="px-4 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد أرصدة إجازات مسجلة.' : 'No leave balances recorded.'}</td></tr>}
              </tbody>
            </table>
          </div>
        </section>
      );
    }

    if (pathname.startsWith('/payroll')) {
      return (
        <div className="space-y-6 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
          <div className="mb-6 flex items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.finance}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.payroll}</h2>
            </div>
            <button type="button" onClick={() => { setActionError(''); setActiveModal('salary'); }} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20">
              {isArabic ? '+ إعداد راتب' : '+ Create salary'}
            </button>
          </div>

          <div className="grid gap-4 md:grid-cols-3">
            <div className="rounded-2xl bg-indigo-50 p-5 transition hover:-translate-y-0.5">
              <p className="text-xs uppercase tracking-[0.18em] text-indigo-500">{isArabic ? 'الراتب الإجمالي' : 'Gross pay'}</p>
              <h3 className="mt-3 text-3xl font-bold text-slate-900">{formatAmount(salaries.reduce((sum, salary) => sum + Number(salary.basic_amount || 0), 0))}</h3>
            </div>
            <div className="rounded-2xl bg-violet-50 p-5 transition hover:-translate-y-0.5">
              <p className="text-xs uppercase tracking-[0.18em] text-violet-500">{isArabic ? 'الخصومات' : 'Deductions'}</p>
              <h3 className="mt-3 text-3xl font-bold text-slate-900">{formatAmount(salaries.reduce((sum, salary) => sum + Number(salary.deductions || 0), 0))}</h3>
            </div>
            <div className="rounded-2xl bg-emerald-50 p-5 transition hover:-translate-y-0.5">
              <p className="text-xs uppercase tracking-[0.18em] text-emerald-500">{isArabic ? 'العلاوات' : 'Bonuses'}</p>
              <h3 className="mt-3 text-3xl font-bold text-slate-900">{formatAmount(salaries.reduce((sum, salary) => sum + Number(salary.bonus || 0), 0))}</h3>
            </div>
          </div>
          {actionError && <p role="alert" className="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
          <div className="overflow-x-auto rounded-2xl border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-start">
              <thead className="bg-slate-50 text-xs text-slate-500"><tr><th className="px-4 py-3">{isArabic ? 'الموظف' : 'Employee'}</th><th className="px-4 py-3">{isArabic ? 'الفترة' : 'Period'}</th><th className="px-4 py-3">{isArabic ? 'الصافي' : 'Net'}</th><th className="px-4 py-3">{isArabic ? 'الحالة' : 'Status'}</th><th className="px-4 py-3">{isArabic ? 'الإجراءات' : 'Actions'}</th></tr></thead>
              <tbody className="divide-y divide-slate-100">{salaries.map((salary) => <tr key={salary.id}><td className="px-4 py-3 text-sm font-medium">{salary.employee?.name || employees.find((employee) => employee.id === salary.employee_id)?.name || `#${salary.employee_id}`}</td><td className="px-4 py-3 text-sm">{salary.month}/{salary.year}</td><td className="px-4 py-3 text-sm">{formatAmount(salary.net_amount || salary.basic_amount)}</td><td className="px-4 py-3 text-sm">{salary.status}</td><td className="px-4 py-3"><div className="flex gap-2">{salary.status === 'pending' && <button type="button" onClick={() => handleSalaryDecision(salary, 'approved')} className="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs text-emerald-800">{isArabic ? 'اعتماد' : 'Approve'}</button>}{salary.status === 'approved' && <button type="button" onClick={() => handlePaySalary(salary)} className="rounded-lg bg-indigo-600 px-2 py-1 text-xs text-white">{isArabic ? 'صرف' : 'Pay'}</button>}</div></td></tr>)}{!salaries.length && <tr><td colSpan="5" className="px-4 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد رواتب مسجلة.' : 'No salary records yet.'}</td></tr>}</tbody>
            </table>
          </div>
        </div>
      );
    }

    if (pathname.startsWith('/documents')) {
      return (
        <div className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
          <div className="mb-6 flex items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.compliance}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.documents}</h2>
            </div>
            <button type="button" onClick={() => { setActionError(''); setDocumentForm({ document_name: '', document_type: 'contract', employee_id: employees[0]?.id ? String(employees[0].id) : '', expiry_date: '', notes: '', file: null }); setActiveModal('document'); }} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20">
              {t.common.uploadFile}
            </button>
          </div>

          <div className="grid gap-4 md:grid-cols-3">
            {documents.map((doc) => {
              const name = doc.document_name || doc.name || 'Document';
              const owner = doc.employee?.name || doc.owner || 'HR Ops';
              const updated = doc.updated_at ? new Date(doc.updated_at).toLocaleDateString() : (doc.updated || 'Recently');
              const status = doc.status || 'Verified';

              return (
                <div key={doc.id ?? name} className="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:shadow-sm">
                  <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-lg font-bold text-white">
                    {name.charAt(0)}
                  </div>
                  <div className="text-lg font-semibold text-slate-900">{name}</div>
                  <div className="mt-2 text-sm text-slate-500">{owner}</div>
                  <div className="mt-4 flex items-center justify-between text-xs uppercase tracking-[0.12em] text-slate-400">
                    <span>{updated}</span>
                    <button type="button" onClick={() => handleDeleteDocument(doc.id)} className="rounded-full bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700">{isArabic ? 'حذف' : 'Delete'}</button>
                  </div>
                </div>
              );
            })}
            {!documents.length && <p className="col-span-full rounded-xl border border-dashed border-slate-300 py-8 text-center text-sm text-slate-500">{isArabic ? 'لا توجد مستندات.' : 'No documents yet.'}</p>}
          </div>
        </div>
      );
    }

    if (pathname.startsWith('/settings')) {
      return (
        <form onSubmit={handleSaveOrganization} className="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
          <div className="mb-6 flex items-center justify-between gap-4">
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.configuration}</p>
              <h2 className="mt-2 text-2xl font-bold text-slate-900">{t.nav.settings}</h2>
            </div>
            <button type="submit" disabled={organizationSaving} className="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 disabled:opacity-50">
              {organizationSaving ? (isArabic ? 'جارٍ الحفظ...' : 'Saving...') : t.common.save}
            </button>
          </div>

          <div className="grid gap-4 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.common.companyName}</label>
              <input required value={organization.company_name || organization.name || ''} onChange={(event) => setOrganization((current) => ({ ...current, company_name: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white" />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.common.timezone}</label>
              <input value={organization.timezone || 'Asia/Riyadh'} onChange={(event) => setOrganization((current) => ({ ...current, timezone: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white" />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.nav.currencyCode}</label>
              <input required maxLength="3" pattern="[A-Z]{3}" value={organization.currency_code || 'SAR'} onChange={(event) => setOrganization((current) => ({ ...current, currency_code: event.target.value.toUpperCase().slice(0, 3) }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white" />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.common.workWeek}</label>
              <div className="flex flex-wrap gap-3">{['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'].map((day) => <label key={day} className="flex items-center gap-2 text-xs text-slate-700"><input type="checkbox" checked={(organization.weekend_days || []).includes(day)} onChange={(event) => setOrganization((current) => ({ ...current, weekend_days: event.target.checked ? [...(current.weekend_days || []), day] : (current.weekend_days || []).filter((item) => item !== day) }))} />{isArabic ? ({ Sunday: 'الأحد', Monday: 'الإثنين', Tuesday: 'الثلاثاء', Wednesday: 'الأربعاء', Thursday: 'الخميس', Friday: 'الجمعة', Saturday: 'السبت' }[day]) : day}</label>)}</div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">{t.common.leaveDays}</label>
              <input type="number" min="0" max="366" value={organization.annual_leave_days || ''} onChange={(event) => setOrganization((current) => ({ ...current, annual_leave_days: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-indigo-500 focus:bg-white" />
            </div>
            {[
              ['work_start_time', isArabic ? 'بداية الدوام' : 'Work start'],
              ['work_end_time', isArabic ? 'نهاية الدوام' : 'Work end'],
              ['day_shift_start_time', isArabic ? 'بداية الوردية النهارية' : 'Day shift start'],
              ['day_shift_end_time', isArabic ? 'نهاية الوردية النهارية' : 'Day shift end'],
              ['night_shift_start_time', isArabic ? 'بداية الوردية الليلية' : 'Night shift start'],
              ['night_shift_end_time', isArabic ? 'نهاية الوردية الليلية' : 'Night shift end'],
            ].map(([field, label]) => <label key={field} className="space-y-2 text-sm font-semibold text-slate-700"><span>{label}</span><input type="time" value={String(organization[field] || '').slice(0, 5)} onChange={(event) => setOrganization((current) => ({ ...current, [field]: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>)}
            {[
              ['late_deduction_rate', isArabic ? 'خصم التأخير (%)' : 'Late deduction (%)'],
              ['absent_deduction_rate', isArabic ? 'خصم الغياب (%)' : 'Absence deduction (%)'],
              ['overtime_rate', isArabic ? 'معامل العمل الإضافي (%)' : 'Overtime rate (%)'],
            ].map(([field, label]) => <label key={field} className="space-y-2 text-sm font-semibold text-slate-700"><span>{label}</span><input type="number" min="0" max="100" step="0.01" value={organization[field] ?? 0} onChange={(event) => setOrganization((current) => ({ ...current, [field]: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>)}
          </div>
          {actionError && <p role="alert" className="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}
        </form>
      );
    }

    return (
      <>
        <div className="grid gap-4 md:grid-cols-4">
          {statCards.map((card) => (
            <div key={card.label} className="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
              <div className="mb-4 flex items-center justify-between text-sm text-slate-500">
                <span>{card.label}</span>
                <span
                  className={`rounded-full px-2 py-1 text-xs font-semibold ${
                    card.tone === 'emerald'
                      ? 'bg-emerald-100 text-emerald-700'
                      : card.tone === 'rose'
                        ? 'bg-rose-100 text-rose-700'
                        : card.tone === 'sky'
                          ? 'bg-sky-100 text-sky-700'
                          : 'bg-violet-100 text-violet-700'
                  }`}
                >
                  {card.change}
                </span>
              </div>
              <div className="text-4xl font-bold text-slate-900">{card.value}</div>
            </div>
          ))}
        </div>

        <div className="mt-6 grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
          <div className="rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-6 text-white shadow-[0_24px_60px_rgba(15,23,42,0.2)]">
            <div className="flex items-start justify-between gap-4">
              <div>
                <p className="text-[10px] font-semibold uppercase tracking-[0.25em] text-slate-300">{isArabic ? 'ملخص المؤسسة' : 'Organization overview'}</p>
                <h3 className="mt-2 text-2xl font-bold">{isArabic ? 'بيانات القوى العاملة' : 'Workforce records'}</h3>
              </div>
              <div className="rounded-full border border-emerald-400/30 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300">
                {isArabic ? 'مباشر' : 'Live'}
              </div>
            </div>

            <div className="mt-6 grid gap-4 md:grid-cols-3">
              <div className="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-300">{isArabic ? 'الموظفون' : 'Employees'}</div>
                <div className="mt-3 text-3xl font-bold">{stats.totalEmployees}</div>
                <div className="mt-1 text-sm text-slate-300">{isArabic ? 'إجمالي السجلات' : 'Total records'}</div>
              </div>
              <div className="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-300">{isArabic ? 'نشطون' : 'Active'}</div>
                <div className="mt-3 text-3xl font-bold">{activeEmployeeCount}</div>
                <div className="mt-1 text-sm text-slate-300">{activeEmployeeRate}%</div>
              </div>
              <div className="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-300">{isArabic ? 'بانتظار الاعتماد' : 'Pending leave'}</div>
                <div className="mt-3 text-3xl font-bold">{pendingLeaves.length}</div>
                <div className="mt-1 text-sm text-slate-300">{isArabic ? 'طلبات إجازة' : 'Leave requests'}</div>
              </div>
            </div>
          </div>

          <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div className="mb-5">
              <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'التوزيع' : 'Distribution'}</p>
              <h3 className="mt-2 text-xl font-bold text-slate-900">{isArabic ? 'الموظفون حسب القسم' : 'Employees by department'}</h3>
            </div>

            <div className="space-y-4">
              {departmentMetrics.map((item) => (
                <div key={item.name}>
                  <div className="mb-1 flex items-center justify-between text-sm">
                    <span className="font-medium text-slate-700">{item.name}</span>
                    <span className="font-semibold text-slate-900">{item.total}</span>
                  </div>
                  <div className="h-2.5 rounded-full bg-slate-100">
                    <div className="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-400" style={{ width: `${employees.length ? Math.round((item.total / employees.length) * 100) : 0}%` }} />
                  </div>
                </div>
              ))}
              {!departmentMetrics.length && <p className="text-sm text-slate-500">{t.common.empty}</p>}
            </div>
          </div>
        </div>

        <div className="mt-6 grid gap-6 xl:grid-cols-[1.7fr_1fr]">
          <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div className="mb-5 flex items-center justify-between">
              <div>
                <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.performance}</p>
                <h3 className="mt-2 text-xl font-bold text-slate-900">{t.common.productivity}</h3>
              </div>
              <button className="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                {t.common.thisYear}
              </button>
            </div>

            <div className="space-y-4 rounded-2xl bg-slate-50 p-5">
              {departmentMetrics.map((department) => (
                <div key={department.name}>
                  <div className="mb-1 flex items-center justify-between text-sm"><span className="font-medium text-slate-700">{department.name}</span><span className="font-semibold text-slate-900">{department.total}</span></div>
                  <div className="h-3 rounded-full bg-slate-200"><div className="h-3 rounded-full bg-gradient-to-r from-indigo-600 to-emerald-500" style={{ width: `${employees.length ? Math.max(2, Math.round((department.total / employees.length) * 100)) : 0}%` }} /></div>
                </div>
              ))}
              {!departmentMetrics.length && <p className="text-sm text-slate-500">{t.common.empty}</p>}
            </div>
          </div>

          <div className="space-y-6">
            <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
              <div className="mb-5">
                <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'الموارد البشرية' : 'Workforce'}</p>
                <h3 className="mt-2 text-xl font-bold text-slate-900">{isArabic ? 'حالة الموظفين' : 'Employee status'}</h3>
              </div>

              <div className="mx-auto mb-5 grid h-36 w-36 place-items-center rounded-full" style={{ background: `conic-gradient(#4f46e5 0 ${activeEmployeeRate}%, #e2e8f0 ${activeEmployeeRate}% 100%)` }}>
                <div className="grid h-24 w-24 place-items-center rounded-full bg-white text-center shadow-inner">
                  <div>
                    <div className="text-2xl font-bold text-slate-900">{activeEmployeeRate}%</div>
                    <div className="text-xs text-slate-500">{isArabic ? 'نشط' : 'Active'}</div>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-3 gap-3 text-center text-sm">
                <div>
                  <div className="text-lg font-bold text-slate-900">{activeEmployeeCount}</div>
                  <div className="text-slate-500">{isArabic ? 'نشط' : 'Active'}</div>
                </div>
                <div>
                  <div className="text-lg font-bold text-slate-900">{employees.filter((employee) => employee.status === 'inactive').length}</div>
                  <div className="text-slate-500">{isArabic ? 'غير نشط' : 'Inactive'}</div>
                </div>
                <div>
                  <div className="text-lg font-bold text-slate-900">{employees.filter((employee) => employee.status === 'terminated').length}</div>
                  <div className="text-slate-500">{isArabic ? 'منتهي الخدمة' : 'Terminated'}</div>
                </div>
              </div>
            </div>

            <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
              <div className="mb-5">
                <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.quickActions}</p>
                <h3 className="mt-2 text-xl font-bold text-slate-900">{t.common.tools}</h3>
              </div>

              <div className="space-y-3 text-sm">
                {quickActions.map((item, index) => (
                  <button
                    key={item}
                    onClick={() => {
                      const next = ['/leaves', '/attendance', '/employees', '/settings'][index];
                      window.history.pushState({}, '', next);
                      setPathname(next);
                    }}
                    className="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 text-left font-medium text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700"
                  >
                    <span>{isArabic ? ['مراجعة طلبات الإجازة', 'مراجعة سجلات الحضور', 'إضافة موظف', 'إعدادات المؤسسة'][index] : item}</span>
                    <span className="text-base">→</span>
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>

        <div className="mt-6 grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
          <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <div className="mb-4 flex items-center justify-between">
              <div>
                <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.people}</p>
                <h3 className="mt-2 text-xl font-bold text-slate-900">{isArabic ? 'أحدث سجلات الموظفين' : 'Employee records'}</h3>
              </div>
              <button onClick={() => { window.history.pushState({}, '', '/employees'); setPathname('/employees'); }} className="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                {t.common.viewAll}
              </button>
            </div>

            <div className="space-y-4">
              {employees.slice(0, 5).map((employee) => (
                    <div key={employee.id} className="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3 transition hover:-translate-y-0.5">
                  <div className="flex items-center gap-3">
                    <div className="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-sm font-bold text-white">
                      {employee.name
                        .split(' ')
                        .map((part) => part[0])
                        .slice(0, 2)
                        .join('')}
                    </div>
                    <div>
                      <div className="font-semibold text-slate-900">{employee.name}</div>
                      <div className="text-sm text-slate-500">{employee.position?.name || employee.position_name || '-'}</div>
                    </div>
                  </div>

                  <div className="text-right">
                    <div className="text-xs uppercase tracking-[0.14em] text-slate-400">{employee.department?.name || employee.department_name || '-'}</div>
                    <div className="mt-1 text-sm font-semibold text-slate-800">{employee.status}</div>
                  </div>
                </div>
              ))}
              {!employees.length && <p className="text-sm text-slate-500">{t.common.empty}</p>}
            </div>
          </div>

          <div className="space-y-6">
            <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
              <div className="mb-4 flex items-center justify-between">
                <div>
                  <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{isArabic ? 'الإجازات' : 'Leave'}</p>
                  <h3 className="mt-2 text-xl font-bold text-slate-900">{isArabic ? 'طلبات تنتظر المراجعة' : 'Requests awaiting review'}</h3>
                </div>
              </div>

              <div className="space-y-3">
                {pendingLeaves.slice(0, 4).map((leave) => (
                  <div key={leave.id} className="rounded-2xl border border-slate-200 bg-slate-50 p-3 transition hover:-translate-y-0.5">
                    <div className="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                      <span>{leave.start_date}</span>
                      <span className="rounded-full bg-amber-100 px-2 py-1 text-[10px] font-bold text-amber-700">{isArabic ? 'قيد الانتظار' : 'Pending'}</span>
                    </div>
                    <div className="mt-2 text-sm font-semibold text-slate-900">{leave.employee?.name || employees.find((employee) => employee.id === leave.employee_id)?.name}</div>
                    <div className="mt-1 text-sm text-slate-500">{leave.leave_type} · {leave.start_date} - {leave.end_date}</div>
                  </div>
                ))}
                {!pendingLeaves.length && <p className="text-sm text-slate-500">{isArabic ? 'لا توجد طلبات معلقة.' : 'No pending requests.'}</p>}
              </div>
            </div>

            <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
              <div className="mb-4">
                <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{t.common.pulse}</p>
                <h3 className="mt-2 text-xl font-bold text-slate-900">{isArabic ? 'عدد الموظفين حسب القسم' : 'Employees by department'}</h3>
              </div>

              <div className="space-y-4">
                {departmentMetrics.map((item) => (
                  <div key={item.name}>
                    <div className="mb-1 flex items-center justify-between text-sm">
                      <span className="font-medium text-slate-700">{item.name}</span>
                      <span className="font-semibold text-slate-900">{item.total}</span>
                    </div>
                    <div className="h-2.5 rounded-full bg-slate-100">
                      <div className="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400" style={{ width: `${employees.length ? Math.max(2, Math.round((item.total / employees.length) * 100)) : 0}%` }} />
                    </div>
                  </div>
                ))}
                {!departmentMetrics.length && <p className="text-sm text-slate-500">{t.common.empty}</p>}
              </div>
            </div>
          </div>
        </div>
      </>
    );
  };

  const navItemsLocalized = navItems.map((item) => ({ ...item, label: item.path === '/' ? t.nav.overview : item.path === '/employees' ? t.nav.employees : item.path === '/users' ? t.nav.users : item.path === '/departments' ? t.nav.departments : item.path === '/positions' ? t.nav.positions : item.path === '/roles' ? t.nav.roles : item.path === '/attendance' ? t.nav.attendance : item.path === '/leaves' ? t.nav.leaves : item.path === '/holidays' ? t.nav.holidays : item.path === '/leave-balances' ? t.nav.leaveBalances : item.path === '/payroll' ? t.nav.payroll : item.path === '/documents' ? t.nav.documents : t.nav.settings }));

  const companyName = organization.company_name || organization.name || (lang === 'ar' ? 'اسم المؤسسة' : 'Organization name');

  const modalTitle = ({
    employee: editingEmployeeId !== null ? (lang === 'ar' ? 'تحديث بيانات الموظف' : 'Edit employee') : (lang === 'ar' ? 'إضافة موظف جديد' : 'Add new employee'),
    attendance: editingAttendanceId !== null ? (lang === 'ar' ? 'تحديث الحضور' : 'Edit attendance') : (lang === 'ar' ? 'تسجيل الحضور' : 'Record attendance'),
    leave: editingLeaveId !== null ? (lang === 'ar' ? 'تحديث طلب الإجازة' : 'Edit leave request') : (lang === 'ar' ? 'طلب إجازة' : 'New leave request'),
    user: editingUserId !== null ? (lang === 'ar' ? 'تعديل حساب المستخدم' : 'Edit user account') : (lang === 'ar' ? 'إضافة مستخدم' : 'Add user'),
    salary: lang === 'ar' ? 'إنشاء سجل راتب' : 'Create salary record',
    document: lang === 'ar' ? 'رفع مستند' : 'Upload document',
    department: editingEntityId !== null ? (lang === 'ar' ? 'تعديل قسم' : 'Edit department') : (lang === 'ar' ? 'إضافة قسم' : 'Add department'),
    position: editingEntityId !== null ? (lang === 'ar' ? 'تعديل مسمى وظيفي' : 'Edit position') : (lang === 'ar' ? 'إضافة مسمى وظيفي' : 'Add position'),
    holiday: editingHolidayId !== null ? (lang === 'ar' ? 'تعديل عطلة رسمية' : 'Edit holiday') : (lang === 'ar' ? 'إضافة عطلة رسمية' : 'Add holiday'),
    leaveBalance: editingLeaveBalanceId !== null ? (lang === 'ar' ? 'تعديل رصيد الإجازة' : 'Edit leave balance') : (lang === 'ar' ? 'إضافة رصيد إجازة' : 'Add leave balance'),
  })[activeModal] || '';

  return (
    <>
      {activeModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
          <div className="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_35px_90px_rgba(15,23,42,0.25)]">
            <div className="mb-6 flex items-center justify-between gap-4">
              <div>
                <p className="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">HR Action</p>
                <h3 className="mt-2 text-2xl font-bold text-slate-900">{modalTitle}</h3>
              </div>
              <button
                type="button"
                onClick={() => setActiveModal(null)}
                className="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-slate-50 text-xl text-slate-600"
              >
                ×
              </button>
            </div>
            {actionError && <p role="alert" className="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{actionError}</p>}

            {activeModal === 'employee' && (
              <form onSubmit={handleSaveEmployee} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2">
                    <span>{lang === 'ar' ? 'حساب المستخدم المرتبط' : 'Linked user account'}</span>
                    <select required value={newEmployeeForm.user_id} onChange={(event) => {
                      const selectedUser = users.find((user) => String(user.id) === event.target.value);
                      setNewEmployeeForm((prev) => ({ ...prev, user_id: event.target.value, name: selectedUser?.name || prev.name, email: selectedUser?.email || prev.email }));
                    }} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900">
                      <option value="">{lang === 'ar' ? 'اختر حسابًا موجودًا' : 'Choose an existing account'}</option>
                      {users.filter((user) => !user.employee || String(user.id) === newEmployeeForm.user_id).map((user) => <option key={user.id} value={user.id}>{user.name} · {user.email}</option>)}
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'الرقم الوظيفي' : 'Employee code'}</span>
                    <input required value={newEmployeeForm.employee_code} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, employee_code: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" />
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'الاسم الكامل' : 'Full name'}</span>
                    <input required value={newEmployeeForm.name} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, name: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" />
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'القسم' : 'Department'}</span>
                    <select required value={newEmployeeForm.department_id} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, department_id: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900">
                      <option value="">{lang === 'ar' ? 'اختر القسم' : 'Select department'}</option>
                      {departments.map((department) => <option key={department.id} value={department.id}>{department.department_name || department.name}</option>)}
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'المسمى الوظيفي' : 'Position'}</span>
                    <select required value={newEmployeeForm.position_id} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, position_id: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900">
                      <option value="">{lang === 'ar' ? 'اختر المسمى' : 'Select position'}</option>
                      {positions.map((position) => <option key={position.id} value={position.id}>{position.position_name || position.name}</option>)}
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'البريد الإلكتروني' : 'Email'}</span><input type="email" required value={newEmployeeForm.email} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, email: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الهاتف' : 'Phone'}</span><input value={newEmployeeForm.phone} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, phone: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الراتب الأساسي' : 'Basic salary'}</span><input type="number" min="0" step="0.01" value={newEmployeeForm.basic_salary} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, basic_salary: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'تاريخ التعيين' : 'Hire date'}</span><input type="date" value={newEmployeeForm.hire_date} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, hire_date: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'تاريخ الميلاد' : 'Birth date'}</span><input type="date" value={newEmployeeForm.birth_date} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, birth_date: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الجنس' : 'Gender'}</span><select value={newEmployeeForm.gender} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, gender: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900"><option value="">-</option><option value="male">{lang === 'ar' ? 'ذكر' : 'Male'}</option><option value="female">{lang === 'ar' ? 'أنثى' : 'Female'}</option></select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الحالة الوظيفية' : 'Employment status'}</span><select value={newEmployeeForm.status} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, status: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900"><option value="active">{lang === 'ar' ? 'نشط' : 'Active'}</option><option value="inactive">{lang === 'ar' ? 'غير نشط' : 'Inactive'}</option><option value="terminated">{lang === 'ar' ? 'منتهي الخدمة' : 'Terminated'}</option></select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الوردية الأساسية' : 'Work shift'}</span><select value={newEmployeeForm.work_shift} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, work_shift: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900"><option value="day">{lang === 'ar' ? 'نهارية' : 'Day'}</option><option value="night">{lang === 'ar' ? 'ليلية' : 'Night'}</option></select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'العنوان' : 'Address'}</span><input value={newEmployeeForm.address} onChange={(event) => setNewEmployeeForm((prev) => ({ ...prev, address: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                </div>

                <div className="flex items-center justify-between gap-3 pt-2">
                  <button
                    type="button"
                    onClick={() => setActiveModal(null)}
                    className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700"
                  >
                    {lang === 'ar' ? 'إلغاء' : 'Cancel'}
                  </button>
                  <button
                    type="submit"
                    disabled={saving}
                    className="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 disabled:opacity-50"
                  >
                    {editingEmployeeId !== null ? (lang === 'ar' ? 'تحديث الموظف' : 'Update employee') : (lang === 'ar' ? 'حفظ الموظف' : 'Save employee')}
                  </button>
                </div>
              </form>
            )}

            {activeModal === 'attendance' && (
              <form onSubmit={handleSaveAttendance} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2">
                    <span>{lang === 'ar' ? 'الموظف' : 'Employee'}</span>
                    <select required value={attendanceForm.employee_id} onChange={(event) => {
                      const employee = employees.find((item) => String(item.id) === event.target.value);
                      setAttendanceForm((prev) => ({ ...prev, employee_id: event.target.value, shift: employee?.work_shift || prev.shift }));
                    }} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900">
                      <option value="">{lang === 'ar' ? 'اختر الموظف' : 'Select employee'}</option>
                      {employees.map((employee) => <option key={employee.id} value={employee.id}>{employee.name} · {employee.employee_code}</option>)}
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'التاريخ' : 'Date'}</span>
                    <input
                      type="date"
                      value={attendanceForm.date}
                      onChange={(e) => setAttendanceForm((prev) => ({ ...prev, date: e.target.value }))}
                      className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-indigo-500"
                    />
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'الوردية' : 'Shift'}</span>
                    <select
                      value={attendanceForm.shift}
                      onChange={(e) => setAttendanceForm((prev) => ({ ...prev, shift: e.target.value }))}
                      className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-indigo-500"
                    >
                      <option value="day">{lang === 'ar' ? 'نهارية' : 'Day'}</option>
                      <option value="night">{lang === 'ar' ? 'ليلية' : 'Night'}</option>
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2">
                    <span>{lang === 'ar' ? 'الحالة' : 'Status'}</span>
                    <select
                      value={attendanceForm.status}
                      onChange={(e) => setAttendanceForm((prev) => ({ ...prev, status: e.target.value }))}
                      className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-indigo-500"
                    >
                      <option value="present">{lang === 'ar' ? 'حاضر' : 'Present'}</option>
                      <option value="late">{lang === 'ar' ? 'متأخر' : 'Late'}</option>
                      <option value="absent">{lang === 'ar' ? 'غائب' : 'Absent'}</option>
                      <option value="leave">{lang === 'ar' ? 'إجازة' : 'Leave'}</option>
                      <option value="holiday">{lang === 'ar' ? 'عطلة' : 'Holiday'}</option>
                    </select>
                  </label>
                </div>

                <div className="flex items-center justify-between gap-3 pt-2">
                  <button
                    type="button"
                    onClick={() => setActiveModal(null)}
                    className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700"
                  >
                    {lang === 'ar' ? 'إلغاء' : 'Cancel'}
                  </button>
                  <button
                    type="submit"
                    className="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25"
                  >
                    {editingAttendanceId !== null ? (lang === 'ar' ? 'تحديث الحضور' : 'Update attendance') : (lang === 'ar' ? 'حفظ الحضور' : 'Save attendance')}
                  </button>
                </div>
              </form>
            )}

            {activeModal === 'leave' && (
              <form onSubmit={handleSaveLeave} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2">
                    <span>{lang === 'ar' ? 'الموظف' : 'Employee'}</span>
                    <select
                      value={leaveForm.employee_id}
                      onChange={(e) => setLeaveForm((prev) => ({ ...prev, employee_id: e.target.value }))}
                      className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-indigo-500"
                    >
                      <option value="">{lang === 'ar' ? 'اختر الموظف' : 'Select employee'}</option>
                      {employees.map((employee) => <option key={employee.id} value={employee.id}>{employee.name} · {employee.employee_code}</option>)}
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'نوع الإجازة' : 'Leave type'}</span>
                    <select
                      value={leaveForm.leave_type}
                      onChange={(e) => setLeaveForm((prev) => ({ ...prev, leave_type: e.target.value }))}
                      className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-indigo-500"
                    >
                      <option value="earned">{lang === 'ar' ? 'سنوية مستحقة' : 'Earned / annual'}</option>
                      <option value="sick">{lang === 'ar' ? 'مرضية' : 'Sick'}</option>
                      <option value="casual">{lang === 'ar' ? 'عارضة' : 'Casual'}</option>
                      <option value="maternity">{lang === 'ar' ? 'أمومة' : 'Maternity'}</option>
                      <option value="paternity">{lang === 'ar' ? 'أبوة' : 'Paternity'}</option>
                      <option value="bereavement">{lang === 'ar' ? 'وفاة' : 'Bereavement'}</option>
                      <option value="unpaid">{lang === 'ar' ? 'بدون راتب' : 'Unpaid'}</option>
                    </select>
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'تاريخ البداية' : 'Start date'}</span>
                    <input required type="date" value={leaveForm.start_date} onChange={(event) => setLeaveForm((prev) => ({ ...prev, start_date: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" />
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700">
                    <span>{lang === 'ar' ? 'تاريخ النهاية' : 'End date'}</span>
                    <input required type="date" min={leaveForm.start_date} value={leaveForm.end_date} onChange={(event) => setLeaveForm((prev) => ({ ...prev, end_date: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" />
                  </label>
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'سبب الطلب' : 'Reason'}</span><textarea rows="3" value={leaveForm.reason} onChange={(event) => setLeaveForm((prev) => ({ ...prev, reason: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900" /></label>
                </div>

                <div className="flex items-center justify-between gap-3 pt-2">
                  <button
                    type="button"
                    onClick={() => setActiveModal(null)}
                    className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700"
                  >
                    {lang === 'ar' ? 'إلغاء' : 'Cancel'}
                  </button>
                  <button
                    type="submit"
                    className="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25"
                  >
                    {editingLeaveId !== null ? (lang === 'ar' ? 'تحديث الطلب' : 'Update request') : (lang === 'ar' ? 'إرسال الطلب' : 'Submit request')}
                  </button>
                </div>
              </form>
            )}

            {activeModal === 'user' && (
              <form onSubmit={handleSaveUser} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الاسم الكامل' : 'Full name'}</span><input required value={userForm.name} onChange={(event) => setUserForm((current) => ({ ...current, name: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{t.common.email}</span><input required type="email" value={userForm.email} onChange={(event) => setUserForm((current) => ({ ...current, email: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'كلمة المرور' : 'Password'}{editingUserId ? (lang === 'ar' ? ' (اتركها فارغة دون تغيير)' : ' (leave blank to keep current)') : ''}</span><input required={!editingUserId} minLength="8" type="password" value={userForm.password} onChange={(event) => setUserForm((current) => ({ ...current, password: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm password'}</span><input required={!editingUserId || Boolean(userForm.password)} minLength="8" type="password" value={userForm.password_confirmation} onChange={(event) => setUserForm((current) => ({ ...current, password_confirmation: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <fieldset className="space-y-2 md:col-span-2"><legend className="text-sm font-semibold text-slate-700">{lang === 'ar' ? 'الأدوار والصلاحيات' : 'Roles and permissions'}</legend><div className="grid gap-2 sm:grid-cols-2">{roles.map((role) => <label key={role.id} className="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm"><input type="checkbox" checked={userForm.role_ids.includes(String(role.id))} onChange={(event) => setUserForm((current) => ({ ...current, role_ids: event.target.checked ? [...current.role_ids, String(role.id)] : current.role_ids.filter((id) => id !== String(role.id)) }))} />{role.display_name || role.role_name}</label>)}</div></fieldset>
                  <label className="flex items-center gap-3 text-sm font-semibold text-slate-700 md:col-span-2"><input type="checkbox" checked={userForm.is_active} onChange={(event) => setUserForm((current) => ({ ...current, is_active: event.target.checked }))} />{lang === 'ar' ? 'الحساب نشط' : 'Account active'}</label>
                </div>
                <div className="flex justify-end gap-3"><button type="button" onClick={() => setActiveModal(null)} className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{lang === 'ar' ? 'إلغاء' : 'Cancel'}</button><button disabled={saving} className="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50">{saving ? (lang === 'ar' ? 'جارٍ الحفظ...' : 'Saving...') : (lang === 'ar' ? 'حفظ المستخدم' : 'Save user')}</button></div>
              </form>
            )}

            {(activeModal === 'department' || activeModal === 'position') && (
              <form onSubmit={handleSaveEntity} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الاسم' : 'Name'}</span><input required maxLength="100" value={entityForm.name} onChange={(event) => setEntityForm((current) => ({ ...current, name: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الرمز' : 'Code'}</span><input required value={entityForm.code} onChange={(event) => setEntityForm((current) => ({ ...current, code: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  {activeModal === 'department' && <>
                    <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'القسم الأعلى' : 'Parent department'}</span><select value={entityForm.parent_department_id} onChange={(event) => setEntityForm((current) => ({ ...current, parent_department_id: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><option value="">{lang === 'ar' ? 'بدون' : 'None'}</option>{departments.filter((item) => item.id !== editingEntityId).map((department) => <option key={department.id} value={department.id}>{department.department_name || department.name}</option>)}</select></label>
                    <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الموقع' : 'Location'}</span><input value={entityForm.location} onChange={(event) => setEntityForm((current) => ({ ...current, location: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  </>}
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'الوصف' : 'Description'}</span><textarea rows="3" value={entityForm.description} onChange={(event) => setEntityForm((current) => ({ ...current, description: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="flex items-center gap-3 text-sm font-medium text-slate-700 md:col-span-2"><input type="checkbox" checked={entityForm.is_active} onChange={(event) => setEntityForm((current) => ({ ...current, is_active: event.target.checked }))} />{lang === 'ar' ? 'نشط' : 'Active'}</label>
                </div>
                <div className="flex justify-end gap-3"><button type="button" onClick={() => setActiveModal(null)} className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{lang === 'ar' ? 'إلغاء' : 'Cancel'}</button><button disabled={saving} className="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50">{saving ? (lang === 'ar' ? 'جارٍ الحفظ...' : 'Saving...') : (lang === 'ar' ? 'حفظ' : 'Save')}</button></div>
              </form>
            )}

            {activeModal === 'salary' && (
              <form onSubmit={handleSaveSalary} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'الموظف' : 'Employee'}</span><select required value={salaryForm.employee_id} onChange={(event) => { const employee = employees.find((item) => String(item.id) === event.target.value); setSalaryForm((current) => ({ ...current, employee_id: event.target.value, basic_amount: employee?.basic_salary || current.basic_amount })); }} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><option value="">{lang === 'ar' ? 'اختر الموظف' : 'Select employee'}</option>{employees.map((employee) => <option key={employee.id} value={employee.id}>{employee.name} · {employee.employee_code}</option>)}</select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'السنة' : 'Year'}</span><input required type="number" min="2000" max="2100" value={salaryForm.year} onChange={(event) => setSalaryForm((current) => ({ ...current, year: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الشهر' : 'Month'}</span><input required type="number" min="1" max="12" value={salaryForm.month} onChange={(event) => setSalaryForm((current) => ({ ...current, month: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الراتب الأساسي' : 'Basic amount'}</span><input required type="number" min="0" step="0.01" value={salaryForm.basic_amount} onChange={(event) => setSalaryForm((current) => ({ ...current, basic_amount: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الخصومات' : 'Deductions'}</span><input type="number" min="0" step="0.01" value={salaryForm.deductions} onChange={(event) => setSalaryForm((current) => ({ ...current, deductions: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'المكافأة' : 'Bonus'}</span><input type="number" min="0" step="0.01" value={salaryForm.bonus} onChange={(event) => setSalaryForm((current) => ({ ...current, bonus: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'مقابل الوقت الإضافي' : 'Overtime amount'}</span><input type="number" min="0" step="0.01" value={salaryForm.overtime_amount} onChange={(event) => setSalaryForm((current) => ({ ...current, overtime_amount: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                </div>
                <div className="flex justify-end gap-3"><button type="button" onClick={() => setActiveModal(null)} className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{lang === 'ar' ? 'إلغاء' : 'Cancel'}</button><button disabled={saving} className="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50">{lang === 'ar' ? 'حفظ سجل الراتب' : 'Save salary'}</button></div>
              </form>
            )}

            {activeModal === 'document' && (
              <form onSubmit={handleSaveDocument} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'اسم المستند' : 'Document name'}</span><input required value={documentForm.document_name} onChange={(event) => setDocumentForm((current) => ({ ...current, document_name: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'نوع المستند' : 'Document type'}</span><input required value={documentForm.document_type} onChange={(event) => setDocumentForm((current) => ({ ...current, document_type: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'الموظف' : 'Employee'}</span><select required value={documentForm.employee_id} onChange={(event) => setDocumentForm((current) => ({ ...current, employee_id: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><option value="">{lang === 'ar' ? 'اختر الموظف' : 'Select employee'}</option>{employees.map((employee) => <option key={employee.id} value={employee.id}>{employee.name} · {employee.employee_code}</option>)}</select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'تاريخ الانتهاء' : 'Expiry date'}</span><input type="date" value={documentForm.expiry_date} onChange={(event) => setDocumentForm((current) => ({ ...current, expiry_date: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الملف PDF أو صورة حتى 10MB' : 'PDF or image file, max 10MB'}</span><input required type="file" accept=".pdf,.jpg,.jpeg,.png" onChange={(event) => setDocumentForm((current) => ({ ...current, file: event.target.files?.[0] || null }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'ملاحظات' : 'Notes'}</span><textarea rows="3" value={documentForm.notes} onChange={(event) => setDocumentForm((current) => ({ ...current, notes: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                </div>
                <div className="flex justify-end gap-3"><button type="button" onClick={() => setActiveModal(null)} className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{lang === 'ar' ? 'إلغاء' : 'Cancel'}</button><button disabled={saving} className="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50">{lang === 'ar' ? 'رفع المستند' : 'Upload document'}</button></div>
              </form>
            )}

            {activeModal === 'holiday' && (
              <form onSubmit={handleSaveHoliday} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'اسم العطلة' : 'Holiday name'}</span><input required maxLength="100" value={holidayForm.holiday_name} onChange={(event) => setHolidayForm((current) => ({ ...current, holiday_name: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'التاريخ' : 'Date'}</span><input required type="date" value={holidayForm.holiday_date} onChange={(event) => setHolidayForm((current) => ({ ...current, holiday_date: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'الوصف' : 'Description'}</span><textarea rows="3" value={holidayForm.description} onChange={(event) => setHolidayForm((current) => ({ ...current, description: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="flex items-center gap-3 text-sm font-medium text-slate-700 md:col-span-2"><input type="checkbox" checked={holidayForm.is_recurring} onChange={(event) => setHolidayForm((current) => ({ ...current, is_recurring: event.target.checked }))} />{lang === 'ar' ? 'تتكرر سنويًا' : 'Repeats every year'}</label>
                </div>
                <div className="flex justify-end gap-3"><button type="button" onClick={() => setActiveModal(null)} className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{lang === 'ar' ? 'إلغاء' : 'Cancel'}</button><button disabled={saving} className="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50">{saving ? (lang === 'ar' ? 'جارٍ الحفظ...' : 'Saving...') : (lang === 'ar' ? 'حفظ العطلة' : 'Save holiday')}</button></div>
              </form>
            )}

            {activeModal === 'leaveBalance' && (
              <form onSubmit={handleSaveLeaveBalance} className="space-y-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <label className="space-y-2 text-sm font-medium text-slate-700 md:col-span-2"><span>{lang === 'ar' ? 'الموظف' : 'Employee'}</span><select required value={leaveBalanceForm.employee_id} onChange={(event) => setLeaveBalanceForm((current) => ({ ...current, employee_id: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><option value="">{lang === 'ar' ? 'اختر الموظف' : 'Select employee'}</option>{employees.map((employee) => <option key={employee.id} value={employee.id}>{employee.name} · {employee.employee_code}</option>)}</select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'السنة' : 'Year'}</span><input required type="number" min="2000" max="2100" value={leaveBalanceForm.year} onChange={(event) => setLeaveBalanceForm((current) => ({ ...current, year: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'نوع الإجازة' : 'Leave type'}</span><select required value={leaveBalanceForm.leave_type} onChange={(event) => setLeaveBalanceForm((current) => ({ ...current, leave_type: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><option value="earned">{lang === 'ar' ? 'سنوية مستحقة' : 'Earned / annual'}</option><option value="sick">{lang === 'ar' ? 'مرضية' : 'Sick'}</option><option value="casual">{lang === 'ar' ? 'عارضة' : 'Casual'}</option><option value="maternity">{lang === 'ar' ? 'أمومة' : 'Maternity'}</option><option value="paternity">{lang === 'ar' ? 'أبوة' : 'Paternity'}</option><option value="bereavement">{lang === 'ar' ? 'وفاة' : 'Bereavement'}</option></select></label>
                  <label className="space-y-2 text-sm font-medium text-slate-700"><span>{lang === 'ar' ? 'الأيام المخصصة' : 'Allocated days'}</span><input required type="number" min="0" step="1" value={leaveBalanceForm.allocated_days} onChange={(event) => setLeaveBalanceForm((current) => ({ ...current, allocated_days: event.target.value }))} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" /></label>
                </div>
                <div className="flex justify-end gap-3"><button type="button" onClick={() => setActiveModal(null)} className="rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{lang === 'ar' ? 'إلغاء' : 'Cancel'}</button><button disabled={saving} className="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50">{saving ? (lang === 'ar' ? 'جارٍ الحفظ...' : 'Saving...') : (lang === 'ar' ? 'حفظ الرصيد' : 'Save balance')}</button></div>
              </form>
            )}
          </div>
        </div>
      )}

      <div dir={lang === 'ar' ? 'rtl' : 'ltr'} className="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.14),_transparent_26%),linear-gradient(180deg,_#eef2ff_0%,_#f8fafc_40%,_#f1f5f9_100%)] p-5 md:p-8">
        <div className="mx-auto max-w-[1500px] overflow-hidden rounded-[32px] border border-slate-200 bg-white/70 shadow-[0_30px_80px_rgba(15,23,42,0.12)] backdrop-blur-xl">
          <div className="grid min-h-[calc(100vh-4rem)] lg:grid-cols-[260px_minmax(0,1fr)]">
            <aside className="bg-slate-950 p-6 text-white">
              <div className="mb-8 flex items-center justify-between gap-3 border-b border-white/10 pb-6">
                <div className="flex items-center gap-3">
                  <div className="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 via-violet-500 to-cyan-400 text-lg font-black shadow-lg shadow-indigo-500/30">
                    H
                  </div>
                  <div>
                    <div className="text-[10px] uppercase tracking-[0.22em] text-slate-300">Company</div>
                    <div className="max-w-44 truncate text-xl font-bold" title={companyName}>{companyName}</div>
                  </div>
                </div>

                <button
                  type="button"
                  onClick={() => setLang((prev) => (prev === 'ar' ? 'en' : 'ar'))}
                  className="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-[10px] font-semibold text-slate-200"
                >
                  {lang === 'ar' ? 'EN' : 'AR'}
                </button>
              </div>

              <div className="mb-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                <div className="flex items-center gap-3">
                  <div className="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-sm font-semibold">
                    {(auth?.name || 'Sarah').split(' ').map((part) => part[0]).slice(0, 2).join('')}
                  </div>
                  <div>
                    <div className="text-sm font-semibold">{auth?.name || 'Sarah Johnson'}</div>
                    <div className="text-xs text-slate-300">People Operations Lead</div>
                  </div>
                </div>
              </div>

              <nav className="space-y-2">
                {navItemsLocalized.map((item) => {
                  const active = pathname === item.path || (item.path === '/' && pathname === '/');

                  return (
                    <button
                      key={item.path}
                      onClick={() => {
                        const next = item.path === '/' ? '/' : item.path;
                        window.history.pushState({}, '', next);
                        setPathname(next);
                      }}
                      className={`flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left text-sm font-medium transition ${
                        active ? 'bg-gradient-to-r from-indigo-500/20 to-violet-500/15 text-white ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white'
                      }`}
                    >
                      <span className="grid h-8 w-8 place-items-center rounded-xl bg-white/8 text-base">
                        {item.path === '/' ? '⌂' : item.path === '/employees' ? '👥' : item.path === '/attendance' ? '🗓' : item.path === '/leaves' ? '📄' : item.path === '/holidays' ? '☀' : item.path === '/leave-balances' ? '◷' : item.path === '/payroll' ? '💰' : item.path === '/documents' ? '📁' : '⚙'}
                      </span>
                      {item.label}
                    </button>
                  );
                })}
              </nav>

              <div className="mt-8 rounded-3xl border border-indigo-500/20 bg-gradient-to-br from-indigo-500/15 to-cyan-500/10 p-4">
                <div className="text-[10px] uppercase tracking-[0.22em] text-indigo-200">{t.common.thisMonth}</div>
                <div className="mt-3 text-3xl font-bold">84.7%</div>
                <div className="mt-1 text-sm text-slate-300">{t.common.efficiency}</div>
              </div>
            </aside>

            <main className="bg-slate-50 p-6 md:p-8">
              <header className="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                  <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">{getCurrentTitle()}</p>
                  <h1 className="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                    {t.common.goodMorning} {auth?.name || 'Sarah'}
                  </h1>
                </div>

                <div className="flex items-center gap-3">
                  <button type="button" onClick={handleExport} className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:shadow-md">
                    {t.common.export}
                  </button>
                  <button onClick={logout} className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:shadow-md">
                    {t.common.logout}
                  </button>
                  <button
                    type="button"
                    onClick={() => openEmployeeModal()}
                    className="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:brightness-110"
                  >
                    {t.common.newEmployee}
                  </button>
                </div>
              </header>

              {renderPage()}
            </main>
          </div>
        </div>
      </div>
    </>
  );
}
